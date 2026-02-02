import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const isLoading = ref(false);
const routeProgress = ref(0);
const showRouteLoader = ref(false);

export function useLoader() {
    const show = () => {
        isLoading.value = true;
    };

    const hide = () => {
        isLoading.value = false;
    };

    const showRoute = () => {
        showRouteLoader.value = true;
        routeProgress.value = 0;
    };

    const hideRoute = () => {
        showRouteLoader.value = false;
        routeProgress.value = 0;
    };

    const updateProgress = (progress: number) => {
        routeProgress.value = Math.min(progress, 100);
    };

    // Listen to Inertia navigation events
    const initializeRouteLoader = () => {
        let progressInterval: NodeJS.Timeout | null = null;

        router.on('start', () => {
            showRoute();
            // Simulate progress
            let progress = 0;
            progressInterval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress >= 90) {
                    progress = 90; // Cap at 90% until finish
                    if (progressInterval) clearInterval(progressInterval);
                }
                updateProgress(progress);
            }, 200);
        });

        router.on('progress', (event) => {
            if (event.detail.progress?.percentage) {
                updateProgress(event.detail.progress.percentage);
            }
        });

        router.on('finish', () => {
            if (progressInterval) clearInterval(progressInterval);
            updateProgress(100);
            setTimeout(() => {
                hideRoute();
            }, 300);
        });

        router.on('exception', () => {
            if (progressInterval) clearInterval(progressInterval);
            hideRoute();
        });
    };

    return {
        isLoading,
        routeProgress,
        showRouteLoader,
        show,
        hide,
        showRoute,
        hideRoute,
        updateProgress,
        initializeRouteLoader,
    };
}
