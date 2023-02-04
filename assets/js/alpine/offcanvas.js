import Alpine from 'alpinejs'
import AlpineFocus from '@alpinejs/focus'
import sprite from '/assets/svg/sprite.svg'

Alpine.plugin( AlpineFocus )

Alpine.data( 'offcanvas', effect => ({
    effect,
    offcanvasOpen: false,
    safeToTrap: false,
    toggleOffcanvas() {
        this.offcanvasOpen = ! this.offcanvasOpen
    },
    closeOffcanvas() {
        this.offcanvasOpen = false
    },
    toggleTrap() {
        this.safeToTrap = ! this.safeToTrap
    },
    canvasEvents: {
        ['x-on:transitionend']() {
            if ( this.effect !== 'reveal' ) return
            this.toggleTrap()
        },
    },
    offcanvasEvents: {
        ['x-on:transitionend']() {
            this.toggleTrap()
        },
        ['x-trap']() {
            return this.safeToTrap
        },
        ['x-on:click.outside']() {
            this.closeOffcanvas()
        },
        ['x-on:keydown.escape.window']() {
            this.closeOffcanvas()
        },
        ['x-bind:class']() {
            return {
                'invisible': ! this.offcanvasOpen && ! this.safeToTrap
            }
        },
    },
    hamburgerIcon() {
        const id = this.offcanvasOpen ? '#close' : '#hamburger'
        return sprite + id
    },
}) )
