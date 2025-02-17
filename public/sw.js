importScripts('https://storage.googleapis.com/workbox-cdn/releases/4.0.0/workbox-sw.js');

workbox.setConfig({
    debug: false
});

workbox.routing.registerRoute(
    /\.html$/,
    new workbox.strategies.NetworkFirst({
        cacheName: 'html-cache',
    })
);

workbox.routing.registerRoute(
    /\.css$/,
    new workbox.strategies.NetworkFirst({
        cacheName: 'css-cache',
    })
);

workbox.routing.registerRoute(
    /\.js$/,
    new workbox.strategies.NetworkFirst({
        cacheName: 'js-cache',
    })
);

workbox.routing.registerRoute(
    /\.(?:png|jpg|jpeg|svg|gif)$/,
    new workbox.strategies.NetworkFirst({
        cacheName: 'image-cache',
    })
);
