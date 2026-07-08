import axios from 'axios';

// Busca produtos na API e devolve apenas o array usado pela renderizacao.
export async function getProducts(apiUrl) {
    try {
        const response = await axios.get(apiUrl);
        return response.data.products;
    } catch (error) {
        const message = error.response?.data?.error || 'Failed to fetch products';
        console.error('Error reading products:', message);
        throw new Error(message);
    }
}
