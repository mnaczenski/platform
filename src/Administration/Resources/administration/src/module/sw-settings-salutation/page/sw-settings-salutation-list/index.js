import { Component, Mixin } from 'src/core/shopware';
import Criteria from 'src/core/data-new/criteria.data';
import template from './sw-settings-salutation-list.html.twig';

Component.register('sw-settings-salutation-list', {
    template,

    inject: ['repositoryFactory', 'context'],

    mixins: [
        Mixin.getByName('listing'),
        Mixin.getByName('placeholder')
    ],

    data() {
        return {
            isLoading: false,
            limit: 10,
            salutations: null,
            sortBy: 'salutationKey',
            sortDirection: 'ASC'
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        columns() {
            return this.getColumns();
        },

        salutationRepository() {
            return this.repositoryFactory.create('salutation');
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.getList();
        },

        getList() {
            this.isLoading = true;
            const criteria = new Criteria(this.page, this.limit);
            criteria.setTerm(this.term);

            this.salutationRepository.search(criteria, this.context).then((searchResult) => {
                this.total = searchResult.total;
                this.salutations = searchResult;
                this.isLoading = false;
            });
        },

        getColumns() {
            return [{
                property: 'salutationKey',
                label: this.$tc('sw-settings-salutation.list.columnSalutationKey'),
                inlineEdit: 'string',
                routerLink: 'sw.settings.salutation.detail',
                primary: true
            }, {
                property: 'displayName',
                label: this.$tc('sw-settings-salutation.list.columnDisplayName'),
                inlineEdit: 'string',
                primary: true
            }, {
                property: 'letterName',
                label: this.$tc('sw-settings-salutation.list.columnLetterName'),
                inlineEdit: 'string'
            }];
        }
    }
});
