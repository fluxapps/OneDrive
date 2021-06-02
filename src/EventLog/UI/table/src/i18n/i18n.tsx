import i18n from "i18next";
import {initReactI18next} from "react-i18next";

const resources = require('./i18n.json')

i18n.use(initReactI18next)
    .init({
        resources,
        // @ts-ignore
        lng: window.lng,
        fallbackLng: "en",
        debug: true,
    });

export default i18n;
