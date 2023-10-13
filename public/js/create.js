const textArea = document.getElementById("textarea");
const charsp = document.getElementById("description-chars");
const description = document.getElementById("description");

textArea.addEventListener("input", OnInput, false);

// Resizes textarea based on content

function OnInput() {
    textArea.style.height = 320;
    textArea.style.height = (this.scrollHeight) + "px";
}

// Updates character count

description.addEventListener("input", () => {
    charsp.innerHTML = description.value.length + "/250";
    if (description.value.length == 250) {
        charsp.classList.add("text-red-500");
    } else {
        charsp.classList.remove("text-red-500");
    }
})