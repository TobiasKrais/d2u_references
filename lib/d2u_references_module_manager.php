<?php
/**
 * Class managing modules published by www.design-to-use.de
 *
 * @author Tobias Krais
 */
class D2UReferencesModules {
	/**
	 * Get modules offered by this addon.
	 * @return D2UModule[] Modules offered by this addon
	 */
	public static function getModules() {
		$modules = [];
		$modules[] = new D2UModule("50-1",
			"D2U Referenzen - Vertikale Referenzboxen ohne Detailansicht",
			3);
		$modules[] = new D2UModule("50-2",
			"D2U Referenzen - Horizontale Referenzboxen mit Detailansicht",
			3);
		$modules[] = new D2UModule("50-3",
			"D2U Referenzen - Horizontale Mini Referenzboxen mit Detailansicht",
			3);
		$modules[] = new D2UModule("50-4",
			"D2U Referenzen - Farbboxen mit seitlichem Bild",
			2);
		return $modules;
	}
}