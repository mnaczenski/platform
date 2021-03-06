import { Component } from 'src/core/shopware';
import template from './sw-newsletter-recipient-filter-switch.html.twig';

Component.register('sw-newsletter-recipient-filter-switch', {
    template,

    props: {
        id: {
            type: String,
            required: true
        },

        label: {
            type: String,
            required: false,
            default: ''
        },

        group: {
            type: String,
            required: false
        }
    },

    methods: {
        onChange(value) {
            this.$emit('change', { id: this.id, group: this.group, value });
        }
    }
});
