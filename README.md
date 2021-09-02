# Inertia.js adapter for Statamic

## Installation

1. Install the package using Composer:

```bash
composer require parallax/inertia-statamic-adapter
```

2. Create your root template in `resources/views/app.blade.php`

```html
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet" />
    <script src="{{ mix('/js/app.js') }}" defer></script>
  </head>
  <body>
    @inertia
  </body>
</html>
```

3. Follow the [Inertia.js documentation](https://inertiajs.com/client-side-setup) for client-side installation

4. Create a `Pages` directory within `/resources/js`

## Component paths

The adapter will use studly-cased collection & blueprint handles to generate the component paths. For example a collection & blueprint with the respective handles of `blog` & `article` the adapter will attempt to locate the component in the following location:

```
/resources/js/Pages/Blog/Article
```

## License

The MIT License (MIT)
