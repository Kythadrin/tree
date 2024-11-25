import {httpDeleteRequest, httpPostRequest} from "../utils/http-requests.ts";

interface IContainer {
    id: number,
    title: string,
    content: string,
    parent?: IContainer,
}

const editSection = async (button: HTMLElement) => {
    const sectionId = button.getAttribute('data-id');
    const sectionTitle = (button.closest('li').querySelector('.section-title') as HTMLElement).innerText;
    const sectionDescription =  (button.closest('li').querySelector('.section-description') as HTMLElement).innerText;

    const newTitle = prompt('Edit title', sectionTitle);
    const newDescription = prompt('Edit description', sectionDescription);

    if (newTitle && newDescription) {
        const response = await httpPostRequest(`/sections/edit/${sectionId}`,{ title: newTitle, description: newDescription })

        if (response.ok) {
            (button.closest('li').querySelector('.section-title') as HTMLElement).innerText = newTitle;
            (button.closest('li').querySelector('.section-description') as HTMLElement).innerText = newDescription;
        } else {
            alert('Error editing section');
        }
    }
};

const deleteSection = async (button: HTMLElement) => {
    const sectionId = button.getAttribute('data-id');
    const sectionElement = button.closest('li');

    if (confirm('Are you sure you want to delete this section? All child sections will be delete too.')) {
        const response = await httpDeleteRequest(`/section/${sectionId}`);

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
            <input class="title" data-title />
            <input class="content" data-content />
            <button class="save-btn" data-action="save"></button>
            <button class="delete-btn" data-action="remove"></button>
        </div>
    `;

    document.querySelector("#sections-list").appendChild(section);
}

const removeSection = (button: HTMLElement) => {
    button.closest("li").remove();
};

const saveSection = async (button: HTMLElement) => {
    const parentId = button.closest("li").getAttribute('data-parent-id');
    const title = (button.parentElement.querySelector('input[data-title]') as HTMLInputElement).value;
    const content = (button.parentElement.querySelector('input[data-content]') as HTMLInputElement).value;

    const response = await httpPostRequest('/api/section', {
        title: title,
        content: content,
        parent: parentId,
    });

    if (response.ok) {
        button.closest("li")?.remove();

        const data: IContainer = await response.json();

        const section = document.createElement("li");
        section.setAttribute('data-id', String(data.id));
        section.innerHTML = `
            <div class="section-item">
                 <div class="data">
                    <span class="title">${data.title}</span>
                    <span class="content">${data.content}</span>
                </div>
                <div class="button-wrapper">
                    <button class="edit-btn" data-id="${data.id}" data-action="edit"></button>
                    <button class="delete-btn" data-id="${data.id}" data-action="delete"></button>
                    <button class="add-btn" data-id="${data.id}" data-action="add-child"></button>
                </div>
            </div>
            <ul></ul>
        `;

        document.querySelector("#sections-list").appendChild(section);
    } else {
        alert('Error adding section');
    }
};

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener("click", (event: MouseEvent) => {
        event.preventDefault();

        const target = event.target as HTMLElement;
        console.log(target.getAttributeNames());
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
                editSection(target).then();
                break;
            case "data-add-child":
                break;
        }
    });
});