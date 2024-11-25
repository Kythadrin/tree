import {httpPostRequest} from "../utils/http-requests.ts";

const formElement = document.querySelector("#registrationForm");
const emailElement = formElement.querySelector("#email") as HTMLInputElement;
const passwordElement = formElement.querySelector("#password") as HTMLInputElement;
const submitButtonElement = formElement.querySelector("#submit");

const registration = async () => {
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

        emailElement.parentElement.appendChild(error);

        return;
    }

    const response = await httpPostRequest("/api/user", {email: email, password: password});

    if (response.ok) {
        document.location.href = "/?reffererUrl=registration";
    } else {
        const data: {message: string} = await response.json();

        const error = document.createElement('div');
        error.classList.add("error");
        error.innerText = data.message;

        formElement.appendChild(error);
    }
}

document.addEventListener("click", (event: MouseEvent) => {
    event.preventDefault();

    const target = event.target as HTMLElement;
    switch (target) {
        case submitButtonElement:
            registration();
            break;
    }
})
