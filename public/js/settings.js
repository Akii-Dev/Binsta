const deleteAccountButton = document.getElementById("delete-knop");
const deleteAccountWarning = document.getElementById("warning");
const deleteAccountWarningMessage = document.getElementById("warning-content");

const newPassword = document.getElementById("new-password");
const confirmNewPassword = document.getElementById("confirm-new-password");
const currentPassword = document.getElementById("current-password");
const passwordButton = document.getElementById("change-password-button");

const imgInp = document.getElementById("user-photo");
const preview = document.getElementById("image");
const removeImg = document.getElementById("remove-pfp");
const hiddenValImg = document.getElementById("remove-status");

const passwordError = document.getElementById("password-error");

let showedError = false;
let madeNew = false;

currentPassword.addEventListener("input", () => {
    madeNew = true;

    if (newPassword.value !== confirmNewPassword.value) {
        passwordError.innerHTML = "Passwords don't match!";
        passwordError.classList.remove("hidden");
        showedError = true;
    } else if (newPassword.value == "" && confirmNewPassword.value == "") {
        passwordError.innerHTML = "Passwords cannot be empty!";
        passwordError.classList.remove("hidden");
        showedError = true;
    } else {
        passwordButton.disabled = false;
        passwordButton.classList.remove("bg-purple-300");
        passwordButton.classList.add("bg-purple-600");
    }
})

newPassword.addEventListener("input", passwordCheck);
confirmNewPassword.addEventListener("input", passwordCheck);

function passwordCheck() {
    if (madeNew == true) {
        if (newPassword.value !== confirmNewPassword.value) {
            passwordError.innerHTML = "Passwords don't match!";
            passwordButton.disabled = true;
            passwordButton.classList.add("bg-purple-300");
            passwordButton.classList.remove("bg-purple-600");
        } else {
            passwordError.classList.add("hidden");
            passwordButton.disabled = false;
            passwordButton.classList.remove("bg-purple-300");
            passwordButton.classList.add("bg-purple-600");
        }
    }
}

deleteAccountButton.addEventListener("click", () => {
    deleteAccountWarning.classList.toggle("hidden")
})

window.addEventListener("click", (e) => {
    if ((!e.target.matches("#delete-knop") && !e.target.matches("#warning-content *")) || e.target.matches("#cancel")) {
        deleteAccountWarning.classList.add("hidden");
    }
})

imgInp.addEventListener("change", (e) => {
    const [file] = imgInp.files
    if (file) {
        preview.src = URL.createObjectURL(file)
    }
    hiddenValImg.value = "false";
})

// Resets the profile picture back to default

removeImg.addEventListener("click", (e) => {
    preview.src = "/images/default-pfp.png";
    hiddenValImg.value = "true";
})