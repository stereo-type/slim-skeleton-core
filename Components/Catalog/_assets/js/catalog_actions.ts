import {
    modal,
    ModalActionType,
    ModalBehaviourHide, ModalBehaviourShow,
    ModalTemplate,
    ModalTemplateAjax,
    ModalTemplateHtml,
    ModalTemplateParams,
    SlimModal
} from "../../../../_assets/js/modal";

import {del, post} from "../../../../_assets/js/ajax";
import {dismissLoader, showLoader} from "../../../../_assets/js/utils";

interface ProgressActions {
    [action: string]: boolean;
}

const _progress_actions: ProgressActions = {};

function is_action_in_progress(id: string, action: string): boolean {
    return _progress_actions.hasOwnProperty(action);
}

type callback = () => void | null;

function run_entity_action(id: string, action: string, url: string, afterSuccess: callback = null): Promise<SlimModal | null> {
    if (!is_action_in_progress(id, action)) {
        return _run_entity_action(id, action, url, afterSuccess);
    } else {
        console.log(`already running ${action}`);
    }
    return null;
}

let formSubmitListener = function (evt: any) {
};


function _show_form_modal(template: ModalTemplate, afterSuccess: callback = null): Promise<SlimModal | null> {
    const modalInstance = modal(template);


    modalInstance.then((modal) => {
        if (modal instanceof SlimModal) {
            const form = modal.getElement().querySelector('#entity_form') as HTMLFormElement | null;

            modal.getElement().addEventListener('hide.bs.modal', function (evt) {
                form.removeEventListener('submit', formSubmitListener);
            });

            let submitting = false;
            formSubmitListener = async function (evt: any) {
                evt.preventDefault();
                const data = new FormData(form);
                showLoader('--form-loader');
                if (!submitting) {
                    submitting = true;
                    try {
                        let response = await post(template.get_route(), data, form);
                        /**Если ошибка, то она должна уже быть обработана*/
                        if (response.ok) {
                            form.removeEventListener('submit', formSubmitListener);
                            const result = await response.json();
                            if (result['success']) {
                                modal.hide();
                                if (afterSuccess != null) {
                                    afterSuccess();
                                }
                                return modal;
                                /**AFTER SUCCESS**/
                            } else {
                                /**На всякий случай*/
                                alert('Ошибка сохранения формы');
                                console.error('Ошибка сохранения формы');
                            }
                        }

                    } finally {
                        dismissLoader('--form-loader');
                        submitting = false;
                    }
                }
            }

            if (form) {
                form.removeEventListener('submit', formSubmitListener);
                const buttonClose = form.querySelector('button[type="button"][action="cancel"]');
                if (buttonClose) {
                    buttonClose.addEventListener('click', function (evt) {
                        evt.preventDefault();
                        modal.hide();
                        return modal;
                    })
                }
                form.addEventListener('submit', formSubmitListener);
            }
        }
    });
    return modalInstance;
}

function _run_edit(id: string, url: string, afterSuccess: callback = null): Promise<SlimModal | null> {
    const template = new ModalTemplateAjax(
        `modal_edit_entity_${id}`,
        url + '/form/' + id,
        {'id': id, 'modal': true},
        new ModalTemplateParams(
            ModalActionType.none,
            'Редактировать',
            null,
            null,
            ModalBehaviourShow.cache,
            ModalBehaviourHide.hide
        )
    );

    return _show_form_modal(template, afterSuccess);
}

function _run_add(url: string, afterSuccess: callback = null): Promise<SlimModal | null> {
    const template = new ModalTemplateAjax(
        `modal_edit_entity_0`,
        url + '/form/0',
        {'id': 0, 'modal': true},
        new ModalTemplateParams(
            ModalActionType.none,
            'Создать',
            null,
            null,
            ModalBehaviourShow.replace,
            ModalBehaviourHide.dispose
        )
    );

    return _show_form_modal(template, afterSuccess);
}

function _run_copy(id: string, url: string, afterSuccess: callback = null): Promise<SlimModal | null> {
    const template = new ModalTemplateAjax(
        `modal_copy_entity_${id}`,
        url + '/form',
        {'id': id, 'modal': true, 'copy': true},
        new ModalTemplateParams(
            ModalActionType.none,
            'Копировать',
            null,
            null,
            ModalBehaviourShow.cache,
            ModalBehaviourHide.hide
        )
    );

    return _show_form_modal(template, afterSuccess);
}

function _run_delete(id: string, url: string, afterSuccess: callback = null): Promise<SlimModal | null> {
    const template = new ModalTemplateHtml(
        `modal_delete_entity_${id}`,
        '<p class="text-center">Вы действительно хотите удалить данный объект?</p>' +
        '<p class="text-center">Действие нельзя отменить.</p>',
        new ModalTemplateParams(
            ModalActionType.save_and_close,
            'Вы уверены?',
            '',
            'modal_confirm.twig',
            ModalBehaviourShow.replace,
            ModalBehaviourHide.dispose
        )
    );

    const modalInstance = modal(template);
    modalInstance.then((modal) => {
        if (modal instanceof SlimModal) {
            const deleteButton = modal.getElement().querySelector('#deleteButton') as HTMLFormElement | null;
            if (deleteButton) {
                deleteButton.addEventListener('click', async evt => {
                    evt.preventDefault();
                    await del(url + '/delete/' + id);
                    modal.hide();
                    if (afterSuccess != null) {
                        afterSuccess();
                    }
                    return modal;
                })
            }
        }

    });
    return modalInstance;
}

function _run_entity_action(id: string, action: string, url: string, afterSuccess: callback = null): Promise<SlimModal | null> {
    switch (action) {
        case 'edit':
            return _run_edit(id, url, afterSuccess);
        case 'copy':
            return _run_copy(id, url, afterSuccess);
        case 'delete':
            return _run_delete(id, url, afterSuccess);
        case 'add':
            return _run_add(url, afterSuccess);
        default:
            console.error(`not specified action ${action}`);
            break;
    }
    return null;
}

export default run_entity_action;