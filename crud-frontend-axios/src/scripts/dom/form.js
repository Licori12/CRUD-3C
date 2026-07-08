import { findProductById } from './render.js';

// Referencias do formulario e elementos de estado visual.
const form = document.getElementById('product-form');
const formTitle = document.getElementById('form-title');
const submitBtn = form.querySelector('button[type="submit"]');
const cancelBtn = document.getElementById('cancel-edit');
const formError = document.getElementById('form-error');

// Estado local do modo edicao.
let editingId = null;
let originalProduct = null;

// Exposto para o app saber se o formulario esta editando.
export function getEditingId() {
    return editingId;
}

// Produto original usado para comparar alteracoes.
export function getOriginalProduct() {
    return originalProduct;
}

// Mostra erro vindo da API no alerta do Bootstrap.
export function showError(message) {
    formError.textContent = message;
    formError.classList.remove('d-none');
}

// Esconde e limpa o alerta de erro.
export function hideError() {
    formError.classList.add('d-none');
    formError.textContent = '';
}

// Preenche o formulario e troca textos para modo edicao.
export function enterEditMode(product) {
    editingId = product.id;
    originalProduct = { ...product };

    document.getElementById('name').value = product.name;
    document.getElementById('price').value = product.price;
    document.getElementById('stock').value = product.stock;

    formTitle.textContent = 'Edit Product';
    submitBtn.textContent = 'Update';
    cancelBtn.style.display = '';

    document.getElementById('name').focus();
}

// Limpa estado e retorna o formulario ao modo criacao.
export function exitEditMode() {
    editingId = null;
    originalProduct = null;
    formTitle.textContent = 'Create Product';
    submitBtn.textContent = 'Create';
    cancelBtn.style.display = 'none';
    form.reset();
}

// A partir do botao clicado, encontra o card e recupera o produto do cache.
export function getProductFromCard(button) {
    const card = button.closest('.product-card');
    return findProductById(Number(card.id));
}
