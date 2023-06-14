const deleteAccountButton = document.getElementById("delete-knop");
const deleteAccountWarning = document.getElementById("warning");
const deleteAccountWarningMessage = document.getElementById("warning-content");

deleteAccountButton.addEventListener("click", () => {
    deleteAccountWarning.classList.toggle("hidden")
})

window.addEventListener("click", (e) => {
    if (!e.target.matches("#delete-knop") && !e.target.matches("#warning-content *")) {
        deleteAccountWarning.classList.add("hidden");
    }
})
