
window.Fusion.booting(function(Vue, router, store) {
	Vue.component('extended-taxonomy-fieldtype', () => import('./ExtendedTaxonomyFieldtype/Field'))
	Vue.component('extended-taxonomy-fieldtype-settings', () => import('./ExtendedTaxonomyFieldtype/Settings'))
})