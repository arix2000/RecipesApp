document.addEventListener('DOMContentLoaded', function () {
    let page = window.pagination.currentPageNumber;
    const totalPages = window.pagination.pageCount;
    const recipeList = document.getElementById('recipe-list');
    const endpoint = window.pagination.endpoint

    console.log("Page:", page);
    console.log("Total Pages:", totalPages);

    function loadMoreRecipes() {
        if (page >= totalPages) return;
        console.log("LOADING: " + endpoint + `page=${page + 1}`)
        fetch(endpoint + `page=${page + 1}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.text())
            .then(data => {
                recipeList.insertAdjacentHTML('beforeend', data);
                page++;
                if (page < totalPages) {
                    attachScrollEvent();
                }
            });
    }

    function attachScrollEvent() {
        window.addEventListener('scroll', onScroll);
    }

    function onScroll() {
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
            window.removeEventListener('scroll', onScroll);
            loadMoreRecipes();
        }
    }

    attachScrollEvent();
});
