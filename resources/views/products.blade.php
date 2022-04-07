@extends('layouts.master')

@section('content')
    <div class="wrapper">
        <aside class="sidebar">
            <div class="logo">
                <div class="logo__img">
                    <img src="/images/logo.svg" alt="/images/logo.svg">
                </div>
                <div class="logo__text">
                    Enterprise Resource Planning
                </div>
            </div>
            <div class="menu__text">Продукты</div>
        </aside>
        <main class="main">
            <header class="header">
                <div class="header__wrapper">
                    <div class="header__item header__item-active">ПРОДУКТЫ</div>
                    <div class="header__item">Иванов Иван Иванович</div>
                </div>
            </header>
            <section class="content">
                <div class="table">
                    <div class="table__header">
                        <div class="table__item">артикул</div>
                        <div class="table__item">название</div>
                        <div class="table__item">статус</div>
                        <div class="table__item">атрибуты</div>
                    </div>
                    @foreach($products as $product)
                        <div class="table__row product" id="product-{{ $product->id }}">
                            <div class="table__item">{{ $product->article }}</div>
                            <div class="table__item">{{ $product->name }}</div>
                            <div class="table__item">{{ $product->status }}</div>
                            <div class="table__item">
                                @foreach($product->data  as $key => $value)
                                    <div>
                                        <span>{{ $key }}: {{ $value}}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="button" id="create__button">Добавить</button>
            </section>
        </main>



        <div id="createModal" class="modal">
            <div class="modal__header">
                <h5 class="modal__title">Добавить продукт</h5>
                <span class="modal__close" id="create__close">✕</span>
             </div>
            <form class="form" name="create" id="create-form">
                <label class="label" for="create__article">Артикул</label>
                <input class="create__input full" type="text" id="create-article" name="article">
                <label class="label" for="create__name">Название</label>
                <input class="create__input full" type="text" id="create-name" name="name">
                <label class="label" for="create__status">Статус</label>
                <select class="select full" id="create-status" name="status">
                    <option value="available">Доступен</option>
                    <option value="unavailable">Недоступен</option>
                </select>
                <div>Атрибуты</div>
                <div class="add-attr" id="create__add-attr">+ Добавить атрибут</div>
                <div id="create-attributes-block"></div>
                <button type="submit" class="button create__submit" id="create__submit">Добавить</button>
            </form>
        </div>

        <div id="edit-product" class="modal">
            <div class="modal__header">
                <h5 class="modal__title" id="edit-title"></h5>
                <span class="modal__close" id="edit__close">✕</span>
            </div>
            <form class="form" name="edit" id="edit-form">
                <label class="label" for="create__article">Артикул</label>
                <input class="create__input full" type="text" id="edit-article" name="article">
                <label class="label" for="create__name">Название</label>
                <input class="create__input full" type="text" id="edit-name" name="name">
                <label class="label" for="create__status">Статус</label>
                <select class="select full" id="edit-status" name="status">
                    <option value="available">Доступен</option>
                    <option value="unavailable">Недоступен</option>
                </select>
                <div>Атрибуты</div>
                <div class="add-attr" id="edit__add-attr">+ Добавить атрибут</div>
                <div id="edit-attributes-block"></div>
                <button type="submit" class="button edit__submit" id="edit__submit">Редактировать</button>
            </form>
        </div>




        <div id="show-product" class="modal">
            <div class="modal__header">
                <h5 class="modal__title" id="show-title"></h5>
                <span class="modal__buttons">
                    <img class="pointer" src="/images/edit.svg" alt="/images/edit.svg" id="edit-icon">
                    <img class="pointer" src="/images/delete-main.svg" alt="/images/delete-main.svg" id="delete-icon">
                </span>
                <span class="modal__close" id="show__close">✕</span>
            </div>
           <div class="modal__item">
               <div class="modal__key">Артикул</div>
               <div class="modal__value" id="show-article"></div>
           </div>
          <div class="modal__item">
              <div class="modal__key">Название</div>
              <div class="modal__value" id="show-name"></div>
          </div>
           <div class="modal__item">
               <div class="modal__key">Статус</div>
               <div class="modal__value" id="show-status"></div>
           </div>
            <div class="modal__item">
                <div class="modal__key">Атрибуты</div>
                <div class="modal__value" id="show-attrubutes"></div>
            </div>
        </div>


    </div>





    <script>
        let currentProductId;//текущий продукт

        const api = async (url, method, body = null) => {
            let params = {
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json",
                },
                method: method,
            }
            if (body !== null) {
                params.body = JSON.stringify(body);
            }
            const response = await fetch(url, params);
            const status = response.status;
            const data =   await response.json();
            return [status, data];
        }



        const titleDiv = document.getElementById('show-title');
        const products = document.querySelectorAll(".product");
        products.forEach(el => {
            const idParts = el.id.split('-');
            const currentId = idParts[idParts.length - 1];

            el.onclick = async () => {
                const [status, data] = await loadProduct(currentId);

                if (status === 200 && data.status === 'success') {
                    const { status, name, article, data: attributes} = data.data;



                    const articleDiv = document.getElementById('show-article');
                    const nameDiv = document.getElementById('show-name');
                    const statusDiv = document.getElementById('show-status');
                    const attributesDiv = document.getElementById('show-attrubutes');


                    titleDiv.insertAdjacentHTML('beforeend', name);
                    articleDiv.insertAdjacentHTML('beforeend', article);
                    nameDiv.insertAdjacentHTML('beforeend', name);
                    statusDiv.insertAdjacentHTML('beforeend', status);

                    let attrText = '';

                    for (let [key, value] of Object.entries(attributes)) {
                        attrText += `<div class="">
                                    <span>${key}</span>
                                    <span>${value}</span>
                                </div>`;
                    }


                    attributesDiv.insertAdjacentHTML('beforeend', attrText);
                }

                showModal.classList.add('show');
            }
        })

        const loadProduct = async id => {
            currentProductId = id;
            return await api(`/api/products/${id}`, "GET")
        }


        const showModal = document.getElementById('show-product');


        const closeShowModal = () => {
            showModal.classList.remove('show');
            const modalValue = document.querySelectorAll(".modal__value");
            const titleDiv = document.getElementById('show-title');
            while (titleDiv.firstChild) {
                titleDiv.removeChild(titleDiv.lastChild);
            }

            modalValue.forEach(value => {
                while (value.firstChild) {
                    value.removeChild(value.lastChild);
                }
            })
        }


        const showCloseButton = document.getElementById('show__close');
        showCloseButton.onclick = () => {
            closeShowModal();
        }



        const deleteProductButton = document.getElementById('delete-icon');
        deleteProductButton.onclick = () => {
            deleteProduct();
        }




        const editCloseButton = document.getElementById('edit__close');

        editCloseButton.onclick = () => {
            closeEditModal();
        }

        const editTitleDiv = document.getElementById('edit-title');

        const closeEditModal = () => {
            editModal.classList.remove('show');
            while (editTitleDiv.firstChild) {
                editTitleDiv.removeChild(editTitleDiv.lastChild);
            }
        }



        const editProductButton = document.getElementById('edit-icon');
        const editModal = document.getElementById('edit-product');
        const editAttributes= document.getElementById('edit-attributes-block');




        const addAttrBlockClosure = () => {
            let number = 0;

            return  (block, name, object = null) => {
                block.insertAdjacentHTML('beforeend',
                    `<div class="attrubutes__item ${name}-attrubutes__item" id="${name}-attribute-${number}">
                    <label class="attrubute label">
                        Название
                        <input class="create__input attributes__input field_name" type="text" name="field_name">
                    </label>
                    <label class="attrubute label">
                        Значение
                        <input class="create__input attributes__input field_value" type="text" name="field_value">
                    </label>
                    <img id="${name}-delete-${number}"  class="delete-btn ${name}-delete-btn" src="images/delete.svg" alt="images/delete.svg">
                </div>`
                )


                if (null !== object) {
                    const currentBlock = document.getElementById(`${name}-attribute-${number}`);
                    currentBlock.querySelector('.field_name').value = object.name;
                    currentBlock.querySelector('.field_value').value = object.value;
                }


                number = number + 1;
                const deletes = document.querySelectorAll(`.${name}-delete-btn`);

                deletes.forEach(el => {
                    const idParts = el.id.split('-');
                    const currentNumber = idParts[idParts.length - 1];
                    const deleteButton = document.getElementById(`${name}-delete-${currentNumber}`);
                    const currentAttribute = document.getElementById(`${name}-attribute-${currentNumber}`);
                    deleteButton.onclick = () => {
                        currentAttribute.remove();
                    }
                })
            }
        }





        const addEditAttributes =  addAttrBlockClosure();

        const editAddAttr = document.getElementById('edit__add-attr');
        editAddAttr.onclick =  () => {
            addEditAttributes(editAttributes, 'edit');
        }



        editProductButton.onclick = async () => {
            showModal.classList.remove('show');
            const [status, data] = await loadProduct(currentProductId);

            if (status === 200 && data.status === 'success') {
                const { status, name, article, data: attributes} = data.data;

                editTitleDiv.insertAdjacentHTML('beforeend', `Редактирование ${name}`);


                const articleDiv = document.getElementById('edit-article');
                const nameDiv = document.getElementById('edit-name');
                const statusDiv = document.getElementById('edit-status');
                articleDiv.value = article;
                nameDiv.value = name;
                statusDiv.value = status;


                for (let [key, value] of Object.entries(attributes)) {
                    addEditAttributes(editAttributes, 'edit', { name: key, value });
                }

            }
            editModal.classList.add('show');
        }






        const deleteProduct = async () => {
            const [status, data] = await api(`/api/products/${currentProductId}`, "DELETE");
            if (data.status === 'success') {
                closeShowModal();
                window.location.reload();
            }
        }






        const createButton = document.getElementById('create__button');
        const createModal = document.getElementById('createModal');
        createButton.onclick = () => {
            createModal.classList.add('show');
        }



        const createAttributesBlock = document.getElementById('create-attributes-block');
        const createCloseButton = document.getElementById('create__close');
        const createAddAttr = document.getElementById('create__add-attr');
        let inputNumber = 1;



        const  addCreateAttributes = addAttrBlockClosure();

        createAddAttr.onclick =  () => {
            addCreateAttributes(createAttributesBlock, 'create');
        }

        createCloseButton.onclick = () => {
            createModal.classList.remove('show');
            while (createAttributesBlock.firstChild) {
                createAttributesBlock.removeChild(createAttributesBlock.lastChild);
            }
        }








        const createErrorMessage = (mode, errors) => {
            for (let [key, value] of Object.entries(errors)) {
                const input = document.getElementById(`${mode}-${key}`);
                let textError = '';
                value.forEach(error => textError += `<div class="error-message">${error}</div>`)
                input.insertAdjacentHTML('afterend', textError);
            }
        }





        const editForm = document.forms.edit;

        editForm.onchange = () => {
            const errorMessages = document.querySelectorAll(".error-message");
            errorMessages.forEach(message => message.remove())
        }

        editForm.onsubmit = async event => {
            event.preventDefault();
            let formData = new FormData(editForm);
            let requestData = {
                id: currentProductId,
                name: formData.get('name'),
                article: formData.get('article'),
                status: formData.get('status'),
            }

            let attributes = document.querySelectorAll(".edit-attrubutes__item");

            let attrs = {};
            attributes.forEach(el => {
                let name =  el.querySelector('.field_name').value;
                attrs[name] = el.querySelector('.field_value').value;
            })

            requestData.data = attrs

            const [status, data] = await api(`/api/products/${currentProductId}`, 'PUT', requestData);


            if (status === 200 && data.status === 'success') {
                editModal.classList.remove('show');
                editForm.reset();
                window.location.reload();
            }  else if (status === 422) {
                createErrorMessage( 'edit', data.errors);
            }
        }







        const createForm = document.forms.create;
        createForm.onchange = () => {
            const errorMessages = document.querySelectorAll(".error-message");
            errorMessages.forEach(message => message.remove())
        }

        createForm.onsubmit = async event => {
            event.preventDefault();
            let formData = new FormData(createForm);
            let requestData = {
                name: formData.get('name'),
                article: formData.get('article'),
                status: formData.get('status'),
            }

            let attributes = document.querySelectorAll(".create-attrubutes__item");

            let attrs = {};
            attributes.forEach(el => {
                let name =  el.querySelector('.field_name').value;
                attrs[name] = el.querySelector('.field_value').value;
            })

            requestData.data = attrs

            const [status, data] = await api("/api/products", 'POST', requestData);


            if (status === 201) {
                createModal.classList.remove('show');
                createForm.reset();
                window.location.reload();
            }  else if (status === 422) {
                createErrorMessage('create', data.errors);
            }
        }

    </script>

@endsection
