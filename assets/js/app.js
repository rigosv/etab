import Vue from 'vue';
import VueI18n from 'vue-i18n';
import Snotify, { SnotifyPosition } from 'vue-snotify';
import BootstrapVue from 'bootstrap-vue';
import fullscreen from 'vue-fullscreen';
//import VueHighlightJS from 'vue-highlight.js';
//import sql from 'highlight.js/lib/languages/sql';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';


// Listado de íconos a utilizar
import { faBan, faBell, faBolt, faBookmark, faChartBar, faCheck, faClipboardList, faClone, faCodeBranch, faCompressArrowsAlt, faCogs, faCog, faChartLine, faDownload, faExpand, faFlag, faFileExport, faFilter,
            faFileExcel, faFilePdf, faFileCsv, faInfoCircle, faPlusSquare, faRulerCombined, faSearch,   faStar, faSync, faSave, faShareAlt,
            faSort, faSortNumericDownAlt, faSortNumericUpAlt, faShare, faSyncAlt, faTable, faTasks, faTh, faTimes, faTimesCircle, faThLarge, faThList
        } from '@fortawesome/free-solid-svg-icons';

library.add(faBan, faBell, faBolt, faBookmark, faChartBar, faCheck, faClipboardList, faClone, faCodeBranch, faCompressArrowsAlt, faCogs, faCog, faChartLine, faDownload, faExpand, faFlag, faFileExport, faFilter,
            faFileExcel, faFilePdf, faFileCsv, faInfoCircle, faPlusSquare, faRulerCombined, faSearch,   faStar, faSync, faSave, faShareAlt,
            faSort, faSortNumericDownAlt, faSortNumericUpAlt, faShare, faSyncAlt, faTable, faTasks, faTh, faTimes, faTimesCircle, faThLarge, faThList
        );
Vue.component('font-awesome-icon', FontAwesomeIcon);
//import {i18n} from './tablero/setup/i18n-setup';
import es from './tablero/locale/es.js';
import en from './tablero/locale/en.js';
const messages = lang==='es' ? es : en;
import Tablero from './tablero/Tablero';
import IndicadorMixin from './tablero/Mixins/IndicadorMixin';

import {store} from './_store';

Vue.use(VueI18n);
Vue.use(Snotify, { toast: { position: SnotifyPosition.rightTop } } );
Vue.use(BootstrapVue);
Vue.use(fullscreen);
//Vue.use(VueHighlightJS,{ languages: {sql}});


const i18n = new VueI18n({
    locale: lang, // set locale
    messages, // set locale messages
});

Vue.filter('normalizarDiacriticos', function (value) {
    if (!value || value == undefined) return '';

    return value.toLowerCase().normalize('NFD')
        .replace(/([aeio])\u0301|(u)[\u0301\u0308]/gi, "$1$2")
        .normalize();
});
Vue.filter('default', function (value, default_) {
    return (value == null ) ? default_ : value ;
});

new Vue({
    store,
    i18n,
    mixins: [IndicadorMixin],
    delimiters: ['<%', '%>'],    
    components: { Tablero},
    
}).$mount('#app');


