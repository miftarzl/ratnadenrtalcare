// Pencarian layanan spesialis gigi
document.addEventListener("DOMContentLoaded", function () {
    const inputCari = document.getElementById("cariSpesialis");
    const listItems = document.querySelectorAll("#listSpesialis li");

    if (inputCari) {
        inputCari.addEventListener("keyup", function () {
            const keyword = this.value.toLowerCase();
            listItems.forEach(function (item) {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(keyword) ? "" : "none";
            });
        });
    }
});
