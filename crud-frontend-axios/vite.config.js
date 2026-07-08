import { defineConfig } from 'vite';

// Configuracao do Vite para desenvolvimento e build do frontend.
export default defineConfig({
  // O index.html fica dentro de src/.
  root: 'src',
  build: {
    // Build final vai para crud-frontend-axios/dist.
    outDir: '../dist',
    emptyOutDir: true
  },
  server: {
    // Permite acesso externo ao container/WSL quando necessario.
    host: '0.0.0.0',
    // Mantem o frontend na mesma porta usada pelo Docker.
    port: 8080
  }
});
