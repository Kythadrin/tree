import {httpPostRequest} from "../utils/http-requests.ts";

const formElement = document.querySelector("#loginForm");
const emailElement = formElement.querySelector("#email") as HTMLInputElement;
const passwordElement = formElement.querySelector("#password") as HTMLInputElement;
const loginButtonElement = formElement.querySelector("#login");
const registrationButtonElement = formElement.querySelector("#registration");

const login = async () => {
    formElement.querySelectorAll(".error").forEach((item) => {
        item.remove();
    });

    const email= emailElement.value;
    const password = passwordElement.value;

    if (email === "") {
        const error = document.createElement('div');
        error.classList.add("error");
        error.innerText = "Field email can't be empty";

        emailElement.parentElement.appendChild(error);

        return;
    }

    if (password === "") {
        const error = document.createElement('div');
        error.classList.add("error");
        error.innerText = "Field password can't be empty";

        passwordElement.parentElement.appendChild(error);

        return;
    }

    const response = await httpPostRequest("/api/login", {email: email, password: password});

    if (response.ok) {
        document.location.href = "/sections";
    } else {
        const data: {message: string} = await response.json();

        const error = document.createElement('div');
        error.classList.add("error");
        error.innerText = data.message;

        formElement.prepend(error);
    }
}

document.addEventListener("click", (event: MouseEvent) => {
    event.preventDefault();

    const target = event.target as HTMLElement;
    switch (target) {
        case loginButtonElement:
            login();
            break;
        case registrationButtonElement:
            document.location.href = "/registration";
            break;
    }
});