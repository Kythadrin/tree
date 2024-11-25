import {httpDeleteRequest, httpPostRequest, httpPutRequest} from "../utils/http-requests.ts";

interface IContainer {
    id: number,
    title: string,
    content: string,
    parent?: number,
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

const editSection = (button: HTMLButtonElement) => {
    const parentId = button.closest("li").getAttribute("data-parent-id") ?? null;
    const item = button.closest(".section-item");
    const id =  button.getAttribute("data-id");

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

const cancelEdit = (button: HTMLButtonElement) => {
    const item = button.closest(".section-item");

    item.innerHTML = createSectionItem(
        button.getAttribute("data-id"),
        (item.querySelector(".title") as HTMLInputElement).value,
        (item.querySelector(".content") as HTMLInputElement).value,
    );
};

const submitEdit = async (button: HTMLButtonElement) => {
    button.disabled = true;

    const section = button.closest(".section-item");
    const id = button.getAttribute("data-id");
    const title = (section.querySelector(".title") as HTMLInputElement).value;
    const content = (section.querySelector(".content") as HTMLInputElement).value;
    const parentId = (section.querySelector(".parentId") as HTMLInputElement).value;
    const sectionContainer = section.closest("li");

    if (id === "" || title === "" || content === "") {
        alert("Id, title and content can't be blank");
        button.disabled = false
        return;
    }

    const response = await httpPutRequest(`/api/section/${id}`,{ title: title, content: content, parent: parentId })

    if (response.ok) {
        const data: IContainer = await response.json();

        section.innerHTML = createSectionItem(String(data.id), data.title, data.content);
        if (data.parent !== undefined) {
            sectionContainer.setAttribute("data-parent-id", String(data.id));
            document.querySelector(`#child-list-${data.parent}`).appendChild(sectionContainer);
        }

        button.disabled = false;
    } else {
        alert("Error editing section");

        button.disabled = false;
    }
};

const deleteSection = async (button: HTMLButtonElement) => {
    button.disabled = true;

    const sectionId = button.getAttribute('data-id');
    const sectionElement = button.closest('li');

    if (confirm("Are you sure you want to delete this section? All child sections will be delete too.")) {
        const response = await httpDeleteRequest(`/api/section/${sectionId}`);

        if (response.ok) {
            sectionElement.remove();

            button.disabled = false;
        } else {
            alert("Error deleting section");

            button.disabled = false;
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
        
        <ul data-child-list></ul>
    `;

    document.querySelector("#sections-list").appendChild(section);
}

const removeSection = (button: HTMLElement) => {
    button.closest("li").remove();
};

const saveSection = async (button: HTMLButtonElement) => {
    button.disabled = true;

    const title = (button.closest("li").querySelector(".title") as HTMLInputElement).value;
    const content = (button.closest("li").querySelector(".content") as HTMLInputElement).value;
    const parentId = (button.closest("li").querySelector(".parentId") as HTMLInputElement).value ?? null;

    if (title === "" || content === "") {
        alert("Title and content can't be empty");
        button.disabled = false
        return;
    }

    const response = await httpPostRequest("/api/section", {
        title: title,
        content: content,
        parent: parentId,
    });

    if (response.ok) {
        const data: IContainer = await response.json();

        const section = button.closest(".section-item");
        const sectionContainer = section.closest("li");
        section.innerHTML = createSectionItem(String(data.id), data.title, data.content);
        sectionContainer.querySelector("[data-child-list]").id = `child-list-${data.id}`

        if (data.parent !== undefined) {
            sectionContainer.setAttribute("data-parent-id", String(data.id));
            document.querySelector(`#child-list-${data.parent}`).appendChild(sectionContainer);
        }

        button.disabled = false;
    } else {
        alert("Error adding section");

        button.disabled = false;
    }
};

const addChildSection = (button: HTMLButtonElement) => {
    const parentId = button.getAttribute('data-id')
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
                    <input class="parentId" value="${parentId}" disabled />
                </label>
            </div>
            <div class="button-wrapper">
                <button class="save-btn" data-action="save"></button>
                <button class="delete-btn" data-action="remove"></button>
            </div>
        </div>
        
        <ul data-child-list></ul>
    `;

    document.querySelector(`#child-list-${parentId}`).appendChild(section);
}

const logout = async (button: HTMLButtonElement) => {
    button.disabled = true;

    const response = await httpPostRequest("/api/logout");

    if (response.ok) {
        document.location.href = "/";
    } else {
        alert("User not logged out. Try again");

        button.disabled = false;
    }
};

document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("click", (event: MouseEvent) => {
        event.preventDefault();

        const target = event.target as HTMLElement;
        switch (target.getAttribute("data-action")) {
            case "add-parent":
                addParentSection();
                break;
            case "remove":
                removeSection(target as HTMLButtonElement);
                break;
            case "save":
                saveSection(target as HTMLButtonElement).then();
                break;
            case "delete":
                deleteSection(target as HTMLButtonElement).then();
                break;
            case "edit":
                editSection(target as HTMLButtonElement);
                break;
            case "submit-edit":
                submitEdit(target as HTMLButtonElement).then();
                break;
            case "cancel-edit":
                cancelEdit(target as HTMLButtonElement);
                break;
            case "add-child":
                addChildSection(target as HTMLButtonElement);
                break;
            case "logout":
                logout(target as HTMLButtonElement).then();
                break;
        }
    });
});