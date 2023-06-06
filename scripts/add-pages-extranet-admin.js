/**
 * Utilisée pour rechercher et filtrer les données d'une table HTML en fonction d'une chaîne de caractères spécifiée.
 * Elle récupère les éléments HTML de la table, parcourt chaque ligne de la table,
 * puis vérifie si la chaîne de caractères recherchée est présente dans l'une des cellules de la ligne.
 * Les lignes qui correspondent à la recherche sont affichées, tandis que les autres lignes sont masquées.
 * @param searchString
 */
function searchInPagesTable(searchString) {
    // Récupération des éléments HTML
    const table = document.querySelector('#pages-list');
    const tbody = table.querySelector('tbody');

    // Récupération des données
    const rows = tbody.querySelectorAll('tr');

    // Filtrage des données
    rows.forEach(function(row) {
        let found = false;
        let cells = row.querySelectorAll('td');

        cells.forEach(function(cell) {
            if (cell.textContent.toLowerCase().indexOf(searchString.toLowerCase()) !== -1) {
                found = true;
            }
        });

        if (found) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
try {
    const search = document.querySelector('#search-page');
    search.addEventListener('input', function() {
        searchInPagesTable(search.value !== ''? search.value: '');
    });
}catch (e){}

