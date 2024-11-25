import {httpDeleteRequest, httpPostRequest, httpPutRequest} from "../utils/http-requests.ts";

interface IContainer {
    id: number,
    title: string,
    content: string,
    parent?: IContainer,
}

const createSectionItem = (id: string, title: string, content: string) => {
    return `
        <div class="id">${id}</div>
        <div class="data">
            <span class="title">${title}</span>
            <span class="content">${content}</span>
        </div>
        <div class="button-wrapper">
            <button class="edit-btn" data-id="${id}" data-action="edit"></button>
            <button class="add-btn" data-id="${id}" data-action="add-child"></button>
            <button class="delete-btn" data-id="${id}" data-action="delete"></button>
        </div>
    `;
};

const editSection = (button: HTMLElement) => {
    const parentId = button.closest("li").getAttribute('data-id') ?? null;
    const item = button.closest(".section-item");
    const id =  button.getAttribute('data-id');

    item.innerHTML = `
        <div class="id">${id}</div>
        <div class="data input">
            <label>
                Title: 
                <input class="title" value="${(item.querySelector('.title') as HTMLElement).innerText}" />
            </label>
            <label>
                Content: 
                 <input class="content" value="${(item.querySelector('.content') as HTMLElement).innerText}" />
            </label>
            <label>
                Parent section id: 
                <input class="parentId" value="${parentId}" />
            </label>
        </div>
        <div class="button-wrapper">
            <button class="save-btn" data-id="${id}" data-action="submit-edit"></button>
            <button class="cancel-btn" data-id="${id}" data-action="cancel-edit"></button>
        </div>
    `;
};

const cancelEdit = (button: HTMLElement) => {
    const item = button.closest(".section-item");

    item.innerHTML = createSectionItem(
        button.getAttribute('data-id'),
        (item.querySelector('.title') as HTMLInputElement).value,
        (item.querySelector('.content') as HTMLInputElement).value,
    );
};

const submitEdit = async (button: HTMLElement) => {
    const item = button.closest(".section-item");
    const id = button.getAttribute('data-id');
    const title = (item.querySelector('.title') as HTMLInputElement).value;
    const content = (item.querySelector('.content') as HTMLInputElement).value;
    const parentId = (item.querySelector('.parentId') as HTMLInputElement).value;

    if (id === "" || title === "" || content === "") {
        alert("Id, title and content can't be blank");

        return;
    }

    const response = await httpPutRequest(`/api/section/${id}`,{ title: title, content: content, parent: parentId })

    if (response.ok) {
        const data: IContainer = await response.json();

        item.innerHTML = createSectionItem(String(data.id), data.title, data.content);
    } else {
        alert('Error editing section');
    }
};

const deleteSection = async (button: HTMLElement) => {
    const sectionId = button.getAttribute('data-id');
    const sectionElement = button.closest('li');

    if (confirm('Are you sure you want to delete this section? All child sections will be delete too.')) {
        const response = await httpDeleteRequest(`/api/section/${sectionId}`);

        if (response.ok) {
            sectionElement.remove();
        } else {
            alert('Error deleting section');
        }
    }
};

const addParentSection = () => {
    const section = document.createElement("li");
    section.innerHTML = `
        <div class="section-item">
            <div class="id"></div>
            <div class="data input">
                <label>
                    Title: 
                    <input class="title" value="" />
                </label>
                <label>
                    Content: 
                    <input class="content" value="" />
                </label>
                <label>
                    Parent section id: 
                    <input class="parentId" value="" />
                </label>
            </div>
            <div class="button-wrapper">
                <button class="save-btn" data-action="save"></button>
                <button class="delete-btn" data-action="remove"></button>
            </div>
        </div>
        
        <ul id="child-list"></ul>
    `;

    document.querySelector("#sections-list").appendChild(section);
}

const removeSection = (button: HTMLElement) => {
    button.closest("li").remove();
};

const saveSection = async (button: HTMLElement) => {
    const parentId = button.closest("li").getAttribute('data-parent-id');
    const title = (button.parentElement.querySelector('.title') as HTMLElement).innerText;
    const content =  (button.parentElement.querySelector('.content') as HTMLElement).innerText;

    if (title === "" || content === "") {
        alert("Title and content can't be empty");
        return;
    }

    const response = await httpPostRequest('/api/section', {
        title: title,
        content: content,
        parent: parentId,
    });

    if (response.ok) {
        const data: IContainer = await response.json();

        const section = button.closest(".section-item");
        section.setAttribute('data-id', String(data.id));
        section.innerHTML = createSectionItem(String(data.id), data.title, data.content);
    } else {
        alert('Error adding section');
    }
};

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener("click", (event: MouseEvent) => {
        event.preventDefault();

        const target = event.target as HTMLElement;
        switch (target.getAttribute('data-action')) {
            case "add-parent":
                addParentSection();
                break;
            case "remove":
                removeSection(target);
                break;
            case "save":
                saveSection(target).then();
                break;
            case "delete":
                deleteSection(target).then();
                break;
            case "edit":
                editSection(target);
                break;
            case "submit-edit":
                submitEdit(target).then();
                break;
            case "cancel-edit":
                cancelEdit(target);
                break;
            case "data-add-child":
                break;
        }
    });
});