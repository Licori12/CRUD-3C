import axios from 'axios';

// Envia um novo produto para a API usando POST.
export async function createProduct(apiUrl, product) {
    try {
        const response = await axios.post(apiUrl, {
            name: product.name,
            price: Number(product.price),
            stock: Number(product.stock),
        });

        return response.data;
    } catch (error) {
        const message = error.response?.data?.error || 'Failed to create product';
        console.error('Error creating product:', message);
        throw new Error(message);
    }
}
