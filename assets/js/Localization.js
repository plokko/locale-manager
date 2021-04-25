class Localization
{

    /**
     *
     * @param {string} locale locale to use
     */
    constructor(locale)
    {
        this.locale = locale;
        this.messages = {};
        this.available_locales = [];
    }

    /**
     * Get current locale from html or body lang tag
     * @returns {string}
     */
    getLocale(){
        return  this.locale || document.documentElement.lang || document.body.lang || 'en';
    }

    /**
     * List available locales in the system, may not be loaded on the page.
     * @returns {string[]}
     */
    availableLocales(){
        return this.available_locales || Object.keys(this.messages);
    }

    /**
     * List laded locales
     * @returns {string[]}
     */
    loadedLocales(){
        return Object.keys(this.messages);
    }

    /**
     * Loads locale messages
     * @param {Object} data Locale data
     * @param {string} locale Language of locale
     * @private
     */
    load(data,locale,available_locales){
        this.messages[locale] = data;
        this.available_locales = available_locales;
    }

    /**
     * Get and replace the string of the given key.
     *
     * @param  {string}  key
     * @param  {object}  replace
     * @param  {string}  locale
     * @return {string}
     */
    trans(key, replace = {},locale)
    {
        return this._replace(this._extract(key,null,locale), replace);
    }

    /**
     * Get and pluralize the strings of the given key.
     *
     * @param  {string}  key
     * @param  {number}  count
     * @param  {object}  replace
     * @param  {string}  locale
     * @return {string}
     */
    trans_choice(key, count = 1, replace = {},locale=null)
    {
        replace.count = count;

        let translations = this._extract(key, '|',locale).split('|'), translation;

        translations.some(t => translation = this._match(t, count));

        translation = translation || (count > 1 ? translations[1] : translations[0]);

        translation = translation.replace(/\[.*?\]|\{.*?\}/, '');

        return this._replace(translation, replace);
    }

    /**
     * Match the translation limit with the count.
     *
     * @param  {string}  translation
     * @param  {number}  count
     * @return {string|null}
     */
    _match(translation, count)
    {
        let match = translation.match(/^[\{\[]([^\[\]\{\}]*)[\}\]](.*)/);

        if (! match) return;

        if (match[1].includes(',')) {
            let [from, to] = match[1].split(',', 2);

            if (to === '*' && count >= from) {
                return match[2];
            } else if (from === '*' && count <= to) {
                return match[2];
            } else if (count >= from && count <= to) {
                return match[2];
            }
        }

        return match[1] == count ? match[2] : null;
    }

    /**
     * Replace the placeholders.
     *
     * @param  {string}  translation
     * @param  {object}  replace
     * @return {string}
     */
    _replace(translation, replace)
    {
        if (typeof translation === 'object') {
            return translation;
        }

        for (let placeholder in replace) {
            translation = translation.toString()
                .replace(`:${placeholder}`, replace[placeholder])
                .replace(`:${placeholder.toUpperCase()}`, replace[placeholder].toString().toUpperCase())
                .replace(
                    `:${placeholder.charAt(0).toUpperCase()}${placeholder.slice(1)}`,
                    replace[placeholder].toString().charAt(0).toUpperCase() + replace[placeholder].toString().slice(1)
                );
        }

        return translation.toString().trim()
    }

    /**
     * Extract values from objects by dot notation.
     *
     * @param  {string}  key Key
     * @param  {mixed}  value Value
     * @param  {string}  locale Locale to use
     * @return {mixed}
     */
    _extract(key, value = null,locale)
    {
        let path = key.toString().split('::'),
            keys = path.pop().toString().split('.');

        if (path.length > 0) {
            path[0] += '::';
        }

        return path.concat(keys).reduce((t, i) => t[i] || (value || key), this._getData(locale));
    }

    /**
     * Get translation data
     * @param {string|null} locale locale
     * @returns {null|Object}
     * @private
     */
    _getData(locale){
        return this.messages[locale || this.getLocale()]||{};
    }
}

export default Localization;
