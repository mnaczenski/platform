import { Component, Mixin } from 'src/core/shopware';
import Criteria from 'src/core/data-new/criteria.data';
import template from './sw-settings-customer-group-list.html.twig';

Component.register('sw-settings-customer-group-list', {
    template,

    inject: ['repositoryFactory', 'context'],

    mixins: [
        Mixin.getByName('listing'),
        Mixin.getByName('placeholder')
    ],

    data() {
        return {
            isLoading: false,
            sortBy: 'name',
            limit: 10,
            customerGroups: null,
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

        customerGroupRepository() {
            return this.repositoryFactory.create('customer_group');
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
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection));

            this.customerGroupRepository.search(criteria, this.context).then((searchResult) => {
                this.total = searchResult.total;
                this.customerGroups = searchResult;
                this.isLoading = false;
            });
        },

        getColumns() {
            return [{
                property: 'name',
                label: this.$tc('sw-settings-customer-group.list.columnName'),
                inlineEdit: 'string',
                routerLink: 'sw.settings.customer.group.detail',
                primary: true
            }, {
                property: 'displayGross',
                label: this.$tc('sw-settings-customer-group.list.columnDisplayGross'),
                inlineEdit: 'boolean'
            }];
        }
    }
});
