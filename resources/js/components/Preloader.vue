<script setup lang="ts">
import { useLoader } from '@/composables/useLoader';

const { isLoading } = useLoader();
</script>

<template>
    <Transition name="preloader-fade">
        <div
            v-if="isLoading"
            class="preloader-overlay"
            role="status"
            aria-live="polite"
            aria-label="Loading"
        >
            <div class="preloader-content">
                <!-- Rotating border -->
                <div class="rotating-border"></div>

                <!-- Logo/Icon with animations -->
                <div class="icon-wrapper">
                    <!-- Custom SVG Logo with fill animation -->
                    <svg
                        class="logo-svg"
                        viewBox="0 0 200 200"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <defs>
                            <clipPath id="fillClip">
                                <rect
                                    x="0"
                                    y="0"
                                    width="200"
                                    height="200"
                                    class="fill-rect"
                                />
                            </clipPath>
                        </defs>

                        <!-- Background shape (unfilled) -->
                        <g opacity="0.3">
                            <circle cx="100" cy="100" r="80" fill="none" stroke="white" stroke-width="4" />
                            <path
                                d="M100,40 L140,80 L140,120 L100,160 L60,120 L60,80 Z"
                                fill="none"
                                stroke="white"
                                stroke-width="3"
                            />
                        </g>

                        <!-- Filled shape (with clip-path animation) -->
                        <g clip-path="url(#fillClip)">
                            <circle cx="100" cy="100" r="80" fill="white" opacity="0.9" />
                            <path
                                d="M100,40 L140,80 L140,120 L100,160 L60,120 L60,80 Z"
                                fill="#bc3863"
                            />
                            <!-- Inner design -->
                            <circle cx="100" cy="100" r="25" fill="white" />
                            <path
                                d="M100,80 L115,95 L100,110 L85,95 Z"
                                fill="#a02d54"
                            />
                        </g>
                    </svg>
                </div>

                <!-- Loading text -->
                <div class="loading-text">
                    <span>L</span>
                    <span>o</span>
                    <span>a</span>
                    <span>d</span>
                    <span>i</span>
                    <span>n</span>
                    <span>g</span>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Preloader overlay */
.preloader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #bc3863 0%, #a02d54 100%);
}

/* Dark mode compatibility */
:global(.dark) .preloader-overlay {
    background: linear-gradient(135deg, #8b2847 0%, #7a2240 100%);
}

/* Content container */
.preloader-content {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2rem;
}

/* Rotating border */
.rotating-border {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 180px;
    height: 180px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: rgba(255, 255, 255, 0.8);
    animation: rotate 2s linear infinite;
}

/* Icon wrapper with pulse animation */
.icon-wrapper {
    position: relative;
    width: 150px;
    height: 150px;
    animation: pulse 2s ease-in-out infinite;
}

/* Logo SVG */
.logo-svg {
    width: 100%;
    height: 100%;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
}

/* Fill rectangle animation */
.fill-rect {
    animation: fillUp 2s ease-in-out infinite;
}

/* Loading text */
.loading-text {
    font-size: 1.5rem;
    font-weight: 600;
    color: white;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    display: flex;
    gap: 0.1em;
}

.loading-text span {
    display: inline-block;
    animation: textFade 1.5s ease-in-out infinite;
}

.loading-text span:nth-child(1) { animation-delay: 0s; }
.loading-text span:nth-child(2) { animation-delay: 0.1s; }
.loading-text span:nth-child(3) { animation-delay: 0.2s; }
.loading-text span:nth-child(4) { animation-delay: 0.3s; }
.loading-text span:nth-child(5) { animation-delay: 0.4s; }
.loading-text span:nth-child(6) { animation-delay: 0.5s; }
.loading-text span:nth-child(7) { animation-delay: 0.6s; }

/* Animations */
@keyframes rotate {
    0% {
        transform: translate(-50%, -50%) rotate(0deg);
    }
    100% {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

@keyframes fillUp {
    0% {
        y: 200;
        height: 0;
    }
    50% {
        y: 0;
        height: 200;
    }
    100% {
        y: 200;
        height: 0;
    }
}

@keyframes textFade {
    0%, 100% {
        opacity: 0.4;
    }
    50% {
        opacity: 1;
    }
}

/* Transition */
.preloader-fade-enter-active {
    transition: opacity 0.3s ease;
}

.preloader-fade-leave-active {
    transition: opacity 0.5s ease;
}

.preloader-fade-enter-from,
.preloader-fade-leave-to {
    opacity: 0;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .rotating-border {
        width: 140px;
        height: 140px;
    }

    .icon-wrapper {
        width: 120px;
        height: 120px;
    }

    .loading-text {
        font-size: 1.25rem;
    }
}
</style>
