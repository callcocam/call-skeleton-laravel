import type { Shelf } from '@planogram/types/planogram';

interface ShelfAreaOptions {
    shelf: Shelf;
    previousShelf?: Shelf;
    scale: number;
    minSpacing?: number;
    minAreaHeight?: number;
}

interface ShelfAreaResult {
    areaStartCm: number;
    areaHeightCm: number;
    areaEndCm: number;
}

export function useShelfAreaCalculation() {
    const calculateShelfArea = ({
        shelf,
        previousShelf,
        scale,
        minSpacing = 2,
        minAreaHeight = 50,
    }: ShelfAreaOptions): ShelfAreaResult => {
        void scale;

        const shelfPosition = shelf.shelf_position;
        const shelfHeightCm = shelf.shelf_height;

        let areaStartCm = 0;

        if (previousShelf) {
            const previousEnd = previousShelf.shelf_position + previousShelf.shelf_height;
            areaStartCm = previousEnd;

            const maxStart = shelfPosition - minSpacing;
            if (areaStartCm > maxStart) {
                areaStartCm = Math.max(shelfHeightCm, maxStart);
            }
        } else {
            areaStartCm = Math.max(shelfHeightCm, shelfPosition - minAreaHeight);
        }

        const areaEndCm = shelfPosition + shelfHeightCm;
        let areaHeightCm = areaEndCm - areaStartCm;

        if (areaHeightCm < minAreaHeight) {
            const newStart = Math.max(0, areaEndCm - minAreaHeight);

            if (previousShelf) {
                const previousEnd = previousShelf.shelf_position + previousShelf.shelf_height;
                areaStartCm = Math.max(previousEnd, newStart);
            } else {
                areaStartCm = newStart;
            }

            areaHeightCm = areaEndCm - areaStartCm;
        }

        return {
            areaStartCm,
            areaHeightCm,
            areaEndCm,
        };
    };

    return {
        calculateShelfArea,
    };
}
