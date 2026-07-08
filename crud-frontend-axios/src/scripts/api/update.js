import axios from 'axios';

// PUT substitui o recurso inteiro com todos os campos.
export async function putProduct(apiUrl, id, product) {
    try {
        const response = await axios.put(`${apiUrl}?id=${id}`, {
            name: product.name,
            price: Number(product.price),
            stock: Number(product.stock),
        });

        return response.data;
    } catch (error) {
        const message = error.response?.data?.error || 'Failed to update product';
        throw new Error(message);
    }
}

// PATCH envia somente os campos alterados.
export async function patchProduct(apiUrl, id, fields) {
    try {
        const response = await axios.patch(`${apiUrl}?id=${id}`, fields);
        return response.data;
    } catch (error) {
        const message = error.response?.data?.error || 'Failed to patch product';
        throw new Error(message);
    }
}

// Decide entre PUT, PATCH ou nenhuma requisicao comparando com o produto original.
export async function updateProduct(apiUrl, id, product, originalProduct) {
    const changedFields = {};

    if (product.name !== originalProduct.name) {
        changedFields.name = product.name;
    }

    if (Number(product.price) !== originalProduct.price) {
        changedFields.price = Number(product.price);
    }

    if (Number(product.stock) !== originalProduct.stock) {
        changedFields.stock = Number(product.stock);
    }

    if (Object.keys(changedFields).length === 0) {
        return null;
    }

    const allChanged = Object.keys(changedFields).length === 3;

    if (allChanged) {
        return putProduct(apiUrl, id, product);
    }

    return patchProduct(apiUrl, id, changedFields);
}
