import promiseFinally from 'promise.prototype.finally';

import mixinAxios from './axios';
import mixinComponent from './component';
import mixinDebug from './debug';
import mixinExtension from './extension';
import mixinLocal from './local';
import mixinModule from './module';
import mixinRouter from './router';
import mixinSidebar from './sidebar';
import mixinUse from './use';

promiseFinally.shim();

export {
    mixinAxios,
    mixinComponent,
    mixinDebug,
    mixinExtension,
    mixinLocal,
    mixinModule,
    mixinRouter,
    mixinSidebar,
    mixinUse,
};