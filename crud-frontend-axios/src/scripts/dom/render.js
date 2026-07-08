import { getProducts } from '../api/read.js';

// Cache da ultima lista carregada, usado para editar/deletar sem novo GET.
let productsCache = [];

// Busca produto no cache local pelo ID.
export function findProductById(id) {
    return productsCache.find((product) => product.id === id);
}

// Carrega produtos da API e renderiza cards Bootstrap.
export async function renderProducts(apiUrl) {
    const products = await getProducts(apiUrl);
    productsCache = products;
    const productsSection = document.getElementById('products');

    if (products.length === 0) {
        productsSection.innerHTML = '<p class="text-muted">No products found.</p>';
        return;
    }

    productsSection.innerHTML = '';

    products.forEach((product) => {
        const productDiv = document.createElement('div');
        productDiv.classList.add('col-md-3');

        productDiv.innerHTML = `
            <div class="card product-card h-100" id="${product.id}">
                <div class="card-body">
                    <h5 class="card-title">${product.name}</h5>
                    <p class="card-text mb-1"><strong>Price:</strong> ${formatPrice(product.price)}</p>
                    <p class="card-text"><strong>Stock:</strong> ${product.stock}</p>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button class="btn btn-sm btn-outline-dark flex-fill" data-action="edit">Edit</button>
                    <button class="btn btn-sm btn-outline-danger flex-fill" data-action="delete">Delete</button>
                </div>
            </div>
        `;

        productsSection.appendChild(productDiv);
    });
}

// Formata preco no padrao brasileiro para melhorar a leitura na interface.
function formatPrice(price) {
    return Number(price).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    });
}
