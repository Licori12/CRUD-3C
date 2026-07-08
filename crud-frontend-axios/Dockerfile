# Nginx serve os arquivos estaticos gerados pelo build do Vite.
FROM nginx:1.27-alpine

# Copia o build final para a pasta publica padrao do Nginx.
COPY dist/ /usr/share/nginx/html/

# Porta HTTP interna do container.
EXPOSE 80
