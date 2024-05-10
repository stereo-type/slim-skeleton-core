import Chart, {ChartDataset, ChartItem, ChartTypeRegistry} from 'chart.js/auto'
import * as ApexCharts from 'apexcharts'
import {post} from "../../../../_assets/js/ajax";

enum ChartLibrary {
    chart = 'chart',
    apex = 'apex',
}

class ChartModel {

    public readonly library: ChartLibrary;
    public readonly wrap: Element;
    public readonly type: keyof ChartTypeRegistry;

    public readonly chartContext: ChartItem;
    public readonly labels: Array<String>;
    public readonly datasets: ChartDataset<keyof ChartTypeRegistry, (number | [number, number])>[];

    public readonly apexContext: HTMLElement;
    public readonly series: Record<string | number, any>;

    public readonly options: Record<string, any>;

    constructor(library: ChartLibrary, wrap: Element, type: any, labels: any, datasets: any, options: any) {
        this.library = library;
        this.wrap = wrap;
        this.type = type;
        this.labels = labels;
        this.datasets = datasets;
        this.series = datasets;
        this.options = options;
        switch (library) {
            case ChartLibrary.chart:
                this.chartContext = document.createElement('canvas');
                wrap.append(this.chartContext);
                break;
            case ChartLibrary.apex:
                this.apexContext = document.createElement('div');
                wrap.append(this.apexContext);
                break;
            default:
                throw new Error('unexpected library');
        }
    }


    static fromArray(chartObject: any): ChartModel {
        return new ChartModel(
            chartObject['library'],
            chartObject['wrap'],
            chartObject['type'],
            chartObject['labels'],
            chartObject['datasets'],
            chartObject['options'] ?? [],
        );
    }
}


function chart(chartObject: ChartModel) {
    new Chart(chartObject.chartContext, {
        type: chartObject.type,
        data: {
            labels: chartObject.labels,
            datasets: chartObject.datasets
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    })
}

function apex(chartObject: ChartModel) {
    // https://apexcharts.com/docs/installation/
    const theme = document.documentElement.dataset.bsTheme ?? 'dark';
    const options = chartObject.options;
    const config = {
        chart: {
            type: chartObject.type,
            animations: options['animations'] ?? {
                enabled: true,
                easing: 'easeinout',
                speed: 500,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            },
        },
        fontFamily: options['fontFamily'] ?? 'inherit',
        height: options['height'] ?? 240,
        parentHeightOffset: options['parentHeightOffset'] ?? 240,
        toolbar: {
            show: false,
        },
        fill: {
            opacity: 1,
        },
        stroke: options['stroke'] ?? {
            width: 2,
            lineCap: "round",
            curve: "smooth",
        },
        tooltip: {
            theme: theme
        },
        series: modify_data(chartObject.series, chartObject.type),
        annotations: options['annotations'] ?? {},
        dataLabels: options['dataLabels'] ?? {
            enabled: false,
        },
        /**Options*/
        xaxis: {
            categories: chartObject.labels
        },
        yaxis: options['yaxis'] ?? {}
    }
    const chart = new ApexCharts(chartObject.apexContext, config)
    chart.render()
}

function isInt(value: any) {
    const x = parseFloat(value);
    return !isNaN(value) && (x | 0) === x;
}

function modify_data(data: Record<string | number, any>, type: String): Record<string | number, any> {
    if (type === 'candlestick') {
        data.forEach(function (outher: any, outherIndex: any) {
            (outher['data'] ?? []).forEach(function (inner: any, innerIndex: any) {
                if (isInt(inner['x'])) {
                    data[outherIndex]['data'][innerIndex]['x'] = new Date(inner['x'] * 1000);
                }
            });
        });
    }
    return data;
}


type DataObject = {
    timelapse: string | number;
    id: string | number;
    [param: string]: any;
}

window.addEventListener('DOMContentLoaded', function () {
    const wraps = document.getElementsByClassName('--chartWrap') as HTMLCollectionOf<Element> | null
    for (const wrap of wraps) {
        const route = wrap.getAttribute('data-route');
        let data: DataObject = {
            timelapse: wrap.getAttribute('data-timelapse') ?? 60,
            id: wrap.getAttribute('data-id') ?? 0,
        };

        for (const attr of wrap.getAttributeNames()) {
            if (attr.startsWith('data-param-')) {
                const param = attr.replace('data-param-', '');
                data[param] = wrap.getAttribute(attr);
            }
        }

        post(route, data).then(response => response.json()).then(chartObject => {
            const library = chartObject['library'] ?? 'undefined'
            const header = chartObject['header'] ?? '';
            const chartWrap = wrap.closest('div.chartWrap');
            const headElement = chartWrap.querySelector('.--header');
            headElement.innerHTML = header;
            switch (library) {
                case 'chart':
                    chart(ChartModel.fromArray(Object.assign(chartObject, {wrap: wrap, library: ChartLibrary.chart})));
                    break;
                case 'apex':
                    apex(ChartModel.fromArray(Object.assign(chartObject, {wrap: wrap, library: ChartLibrary.apex})));
                    break;
                default:
                    throw new Error('unsupported library ' + library);
            }
        })
    }
})

export {
    ChartModel, ChartLibrary,
    chart, apex,
}