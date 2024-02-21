/**
 * @category    Halex
 * @package     Halex\CustomerApproval
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

define([
    'underscore',
    'Magento_Ui/js/grid/columns/column'
], function (_, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html'
        },

        /**
         * Retrieves label associated with a provided value.
         *
         * @returns {String}
         */
        getLabel: function () {
            var options = this.options || [],
                values = this._super(),
                label = [];

            if (_.isString(values)) {
                values = values.split(',');
            }

            if (!_.isArray(values)) {
                values = [values];
            }

            values = values.map(function (value) {
                return value + '';
            });

            options = this.flatOptions(options);

            options.forEach(function (item) {
                if (_.contains(values, item.value + '')) {
                    label.push(
                        `<div class="customer-approval-state ${ item.value === '1' ? 'approved' : 'not-approved' }">`
                        + item.label
                        + '</div>'
                    );
                }
            });

            return label.join(', ');
        },

        /**
         * Transformation tree options structure to liner array.
         *
         * @param {Array} options
         * @returns {Array}
         */
        flatOptions: function (options) {
            var self = this;

            if (!_.isArray(options)) {
                options = _.values(options);
            }

            return options.reduce(function (opts, option) {
                if (_.isArray(option.value)) {
                    opts = opts.concat(self.flatOptions(option.value));
                } else {
                    opts.push(option);
                }

                return opts;
            }, []);
        },

        /**
         * Returns path to the columns' body template.
         *
         * @returns {String}
         */
        getBody: function () {
            return 'ui/grid/cells/html';
        },
    });
});
