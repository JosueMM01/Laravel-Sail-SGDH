import './bootstrap'

import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'
import PerfectScrollbar from 'perfect-scrollbar'

window.PerfectScrollbar = PerfectScrollbar

document.addEventListener('alpine:init', () => {
    Alpine.data('mainState', () => {
        let lastScrollTop = 0
        const init = function () {
            window.addEventListener('scroll', () => {
                let st =
                    window.pageYOffset || document.documentElement.scrollTop
                if (st > lastScrollTop) {
                    // downscroll
                    this.scrollingDown = true
                    this.scrollingUp = false
                } else {
                    // upscroll
                    this.scrollingDown = false
                    this.scrollingUp = true
                    if (st == 0) {
                        //  reset
                        this.scrollingDown = false
                        this.scrollingUp = false
                    }
                }
                lastScrollTop = st <= 0 ? 0 : st // For Mobile or negative scrolling
            })
        }

        return {
            init,
            isSidebarOpen: window.innerWidth > 1024,
            isSidebarHovered: false,
            handleSidebarHover(value) {
                if (window.innerWidth < 1024) {
                    return
                }
                this.isSidebarHovered = value
            },
            handleWindowResize() {
                if (window.innerWidth <= 1024) {
                    this.isSidebarOpen = false
                } else {
                    this.isSidebarOpen = true
                }
            },
            scrollingDown: false,
            scrollingUp: false,
        }
    })
})

Alpine.plugin(collapse)

Alpine.start()

const initPasswordVisibilityToggles = () => {
    const updateButtonState = (button, isVisible) => {
        const showIcon = button.querySelector('[data-password-icon="show"]')
        const hideIcon = button.querySelector('[data-password-icon="hide"]')
        const showLabel = button.dataset.passwordLabelShow || 'Mostrar contraseña'
        const hideLabel = button.dataset.passwordLabelHide || 'Ocultar contraseña'

        if (showIcon && hideIcon) {
            showIcon.classList.toggle('hidden', isVisible)
            hideIcon.classList.toggle('hidden', !isVisible)
        }

        button.setAttribute('aria-label', isVisible ? hideLabel : showLabel)
        button.setAttribute('aria-pressed', isVisible ? 'true' : 'false')
    }

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        const targetId = button.getAttribute('data-password-target')
        if (!targetId) {
            return
        }

        const input = document.getElementById(targetId)
        if (!input) {
            return
        }

        button.addEventListener('click', () => {
            const showing = input.type === 'text'
            input.setAttribute('type', showing ? 'password' : 'text')
            updateButtonState(button, !showing)
        })

        updateButtonState(button, input.type === 'text')
    })
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPasswordVisibilityToggles)
} else {
    initPasswordVisibilityToggles()
}
