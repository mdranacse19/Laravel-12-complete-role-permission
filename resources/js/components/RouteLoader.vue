<script setup lang="ts">
import { useLoader } from '@/composables/useLoader';
import { computed } from 'vue';

const { showRouteLoader, routeProgress } = useLoader();

const progressWidth = computed(() => `${routeProgress.value}%`);
const progressLabel = computed(() => Math.round(routeProgress.value));
</script>

<template>
    <Transition name="route-loader-fade">
        <div v-if="showRouteLoader" class="route-loader">
            <!-- Top progress bar -->
            <div class="progress-bar-container">
                <div
                    class="progress-bar"
                    :style="{ width: progressWidth }"
                    role="progressbar"
                    :aria-valuenow="routeProgress"
                    aria-valuemin="0"
                    aria-valuemax="100"
                ></div>
            </div>

            <!-- Centered spinner overlay -->
            <div class="spinner-overlay">
                <div class="spinner-content">
                    <!-- Animated spinner -->
                    <div class="spinner-circle">
                        <svg class="spinner-svg" viewBox="0 0 100 100">
                            <defs>
                                <linearGradient id="spinnerGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color: #3b82f6; stop-opacity: 1" />
                                    <stop offset="50%" style="stop-color: #8b5cf6; stop-opacity: 1" />
                                    <stop offset="100%" style="stop-color: #ec4899; stop-opacity: 1" />
                                </linearGradient>
                            </defs>
                            <circle
                                cx="50"
                                cy="50"
                                r="40"
                                fill="none"
                                stroke-width="6"
                                class="spinner-track"
                            />
                            <circle
                                cx="50"
                                cy="50"
                                r="40"
                                fill="none"
                                stroke-width="6"
                                class="spinner-progress"
                                :style="{
                                    strokeDashoffset: 251.2 - (251.2 * routeProgress) / 100
                                }"
                            />
                        </svg>
                    </div>

                    <!-- Progress percentage -->
                    <!-- <div class="progress-percentage">
                        {{ progressLabel }}<span class="percent-sign">%</span>
                    </div> -->

                    <!-- Loading text -->
                    <div class="route-loading-text">Loading...</div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Route loader container */
.route-loader {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9998;
    pointer-events: none;
}

/* Top progress bar */
.progress-bar-container {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: transparent;
    z-index: 9999;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
    transition: width 0.2s ease;
    box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
}

/* Spinner overlay */
.spinner-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(4px);
    pointer-events: auto;
}

/* Dark mode */
:global(.dark) .spinner-overlay {
    background: rgba(0, 0, 0, 0.8);
}

/* Spinner content */
.spinner-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 2rem;
    border-radius: 1rem;
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

:global(.dark) .spinner-content {
    background: rgba(30, 30, 30, 0.95);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

/* Spinner circle */
.spinner-circle {
    position: relative;
    width: 80px;
    height: 80px;
    animation: spinnerRotate 2s linear infinite;
}

.spinner-svg {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.spinner-track {
    stroke: rgba(156, 163, 175, 0.3);
}

:global(.dark) .spinner-track {
    stroke: rgba(75, 85, 99, 0.5);
}

.spinner-progress {
    stroke: url(#spinnerGradient);
    stroke-linecap: round;
    stroke-dasharray: 251.2;
    stroke-dashoffset: 251.2;
    transition: stroke-dashoffset 0.2s ease;
}

/* Gradient definition for spinner */
.spinner-svg::before {
    content: '';
}

/* Progress percentage */
.progress-percentage {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 50%, #ec4899 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
}

.percent-sign {
    font-size: 1.5rem;
}

/* Loading text */
.route-loading-text {
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
    animation: textPulse 1.5s ease-in-out infinite;
}

:global(.dark) .route-loading-text {
    color: #9ca3af;
}

/* Animations */
@keyframes spinnerRotate {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

@keyframes textPulse {
    0%, 100% {
        opacity: 0.5;
    }
    50% {
        opacity: 1;
    }
}

/* Transitions */
.route-loader-fade-enter-active {
    transition: opacity 0.2s ease;
}

.route-loader-fade-leave-active {
    transition: opacity 0.3s ease;
}

.route-loader-fade-enter-from,
.route-loader-fade-leave-to {
    opacity: 0;
}

/* Add gradient for spinner stroke */
.spinner-svg {
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

/* Responsive */
@media (max-width: 640px) {
    .spinner-circle {
        width: 60px;
        height: 60px;
    }

    .progress-percentage {
        font-size: 1.5rem;
    }

    .percent-sign {
        font-size: 1.125rem;
    }

    .spinner-content {
        padding: 1.5rem;
    }
}
</style>
