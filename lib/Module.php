<?php

namespace TobiasKrais\D2UReferences;

/**
 * Class managing modules published by www.design-to-use.de.
 *
 * @author Tobias Krais
 */
class Module
{
    /**
     * Get modules offered by this addon.
     * @return \TobiasKrais\D2UHelper\Module[] Modules offered by this addon
     */
    public static function getModules()
    {
        $modules = [];
        $modules[] = new \TobiasKrais\D2UHelper\Module('50-1',
            'D2U Referenzen - Vertikale Referenzboxen ohne Detailansicht',
            4);
        $modules[] = new \TobiasKrais\D2UHelper\Module('50-2',
            'D2U Referenzen - Horizontale Referenzboxen mit Detailansicht',
            4);
        $modules[] = new \TobiasKrais\D2UHelper\Module('50-3',
            'D2U Referenzen - Horizontale Mini Referenzboxen mit Detailansicht',
            5);
        $modules[] = new \TobiasKrais\D2UHelper\Module('50-4',
            'D2U Referenzen - Farbboxen mit seitlichem Bild',
            4);
        return $modules;
    }
}
