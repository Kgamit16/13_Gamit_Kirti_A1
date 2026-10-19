async function loadProducts(categoryId, page = 1) {


const results = document.getElementById("product-results");

if (!results) {
    return;
}

results.innerHTML = "<p>Loading products...</p>";

try {

    const response = await fetch(
        "fetch_products.php?category_id=" +
        encodeURIComponent(categoryId) +
        "&page=" +
        encodeURIComponent(page)
    );

    const html = await response.text();

    if (!response.ok) {
        throw new Error(html || "Unable to load products.");
    }

    results.innerHTML = html;

} catch (error) {

    results.textContent = error.message;

}


}

document.addEventListener("DOMContentLoaded", function () {

```
const results = document.getElementById("product-results");

if (!results) {
    return;
}

if (typeof categoryId !== "undefined") {
    loadProducts(categoryId, 1);
}

results.addEventListener("click", function (event) {

    const button = event.target.closest(".page-button");

    if (!button) {
        return;
    }

    const currentCategory = typeof categoryId !== "undefined"
        ? categoryId
        : new URLSearchParams(window.location.search)
            .get("category_id");

    if (currentCategory) {
        loadProducts(currentCategory, button.dataset.page);
    }

});


});
