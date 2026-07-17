# Development images

Store temporary images used only during local layout development in this directory.

While the Vite development server is running, reference them from WordPress templates
through the Vite server, for example:

```php
<img src="http://localhost:5173/src/images/hero-placeholder.webp" alt="">
```

The `src` directory is not copied into the release ZIP. Do not import temporary images
from CSS or TypeScript, because imported files are included in the production Vite build.
