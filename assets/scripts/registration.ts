import {httpPostRequest} from "./utils/http-requests.ts";

const form = document.querySelector("#registrationForm");
const emailElement = form.querySelector("#email") as HTMLInputElement;
const passwordElement = form.querySelector("#password") as HTMLInputElement;

document.addEventListener("click", (event: MouseEvent) => {
    const target = event.target as HTMLElement;
    if (target.getAttribute("id") === "submit") {
        event.preventDefault();

        form.querySelectorAll(".error").forEach((item) => {
            item.remove();
        });

        const email= emailElement.value;
        const password = passwordElement.value;

        if (email.length <= 0) {
            const error = document.createElement('span');
            error.classList.add("error");
            error.innerText = "Field email can't be empty";

            emailElement.parentElement.appendChild(error);

            return;
        }
        if (password.length <= 0) {
            const error = document.createElement('span');
            error.classList.add("error");
            error.innerText = "Field password can't be empty";

            emailElement.parentElement.appendChild(error);

            return;
        }

        httpPostRequest("/register", {email: email, password: password}).then(() => {
            document.location.href = "/?reffererUrl=registration";
        });
    }
})
