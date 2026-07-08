// Importa funcoes de DOM e API que o app principal orquestra.
import { renderProducts } from './scripts/dom/render.js';
import {
    enterEditMode, exitEditMode, getEditingId, getOriginalProduct,
    getProductFromCard, showError, hideError,
} from './scripts/dom/form.js';
import { createProduct } from './scripts/api/create.js';
import { updateProduct } from './scripts/api/update.js';
import { deleteProduct } from './scripts/api/delete.js';

// URL da API; pode ser sobrescrita por variavel de ambiente do Vite.
const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api/products';

// Elementos fixos da pagina usados pelos eventos.
const form = document.getElementById('product-form');
const cancelBtn = document.getElementById('cancel-edit');
const productsSection = document.getElementById('products');

// Delegacao de eventos: um listener no container atende botoes criados dinamicamente.
productsSection.addEventListener('click', async (event) => {
    const target = event.target;

    if (target.dataset.action === 'edit') {
        enterEditMode(getProductFromCard(target));
    }

    if (target.dataset.action === 'delete') {
        const product = getProductFromCard(target);

        if (!confirm('Are you sure you want to delete this product?')) {
            return;
        }

        try {
            await deleteProduct(apiUrl, product.id);

            // Se o produto deletado estava em edicao, limpa o formulario.
            if (getEditingId() === product.id) {
                exitEditMode();
            }

            renderProducts(apiUrl);
        } catch (error) {
            showError(error.message);
        }
    }
});

// Primeira renderizacao da lista e botao para cancelar modo edicao.
document.addEventListener('DOMContentLoaded', () => renderProducts(apiUrl));
cancelBtn.addEventListener('click', exitEditMode);

// Submit cria produto novo ou atualiza o produto em edicao.
form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const name = document.getElementById('name').value;
    const price = document.getElementById('price').value;
    const stock = document.getElementById('stock').value;

    hideError();

    try {
        const editingId = getEditingId();
        const product = { name, price, stock };

        if (editingId !== null) {
            const result = await updateProduct(apiUrl, editingId, product, getOriginalProduct());

            // Null significa que nenhum campo foi alterado.
            if (result === null) {
                exitEditMode();
                return;
            }
        } else {
            await createProduct(apiUrl, product);
        }

        exitEditMode();
        renderProducts(apiUrl);
    } catch (error) {
        showError(error.message);
    }
});
