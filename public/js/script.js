const profilePic = document.getElementById("user-menu-button");
const profileMenu = document.getElementById("profile-menu");

const mobileButton = document.getElementById("burger-button");
const mobileNav = document.getElementById("mobile-nav");

profilePic.addEventListener("click", () => {
    profileMenu.classList.toggle("hidden")
})

mobileButton.addEventListener("click", () => {
    mobileNav.classList.toggle("hidden")
})

window.addEventListener("click", (e) => {
    if (!e.target.matches("#user-menu-button *") && !e.target.matches("#profile-menu *")) {
        profileMenu.classList.add("hidden");
    }
    if (!e.target.matches("#burger-button *") && !e.target.matches("#mobile-nav *")) {
        mobileNav.classList.add("hidden");
    }
})

