<?php

namespace App\Services;

/**
 * Classe TemplateEngine - Moteur de templates simple
 * Remplace les variables {{variable}} par leurs valeurs
 */
class TemplateEngine
{
    /**
     * Rendre un template avec des variables
     * @param string $templatePath Chemin vers le fichier template
     * @param array $variables Tableau associatif de variables
     * @return string Contenu du template avec variables remplacées
     */
    public static function render(string $templatePath, array $variables): string
    {
        if (!file_exists($templatePath)) {
            throw new \Exception("Template non trouvé : {$templatePath}");
        }
        
        $template = file_get_contents($templatePath);
        
        // Remplacer chaque variable {{nom}} par sa valeur
        foreach ($variables as $key => $value) {
            // Convertir les arrays en JSON formaté
            if (is_array($value)) {
                $value = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
            
            // Convertir les objets en string si possible
            if (is_object($value) && method_exists($value, '__toString')) {
                $value = (string) $value;
            }
            
            // Remplacer la variable dans le template
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        
        // Nettoyer les variables non remplacées (optionnel)
        $template = preg_replace('/\{\{[^}]+\}\}/', '', $template);
        
        return $template;
    }
    
    /**
     * Vérifier si un template existe
     * @param string $templatePath Chemin vers le fichier template
     * @return bool True si le template existe
     */
    public static function exists(string $templatePath): bool
    {
        return file_exists($templatePath);
    }
}

