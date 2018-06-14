import Router from './Router';

export default function route(name, params, absolute, customZiggy) {
    return new Router(name, params, absolute, customZiggy);
};
