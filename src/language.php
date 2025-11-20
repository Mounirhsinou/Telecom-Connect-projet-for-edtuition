<?php
/**
 * Multilingual Support System
 * 
 * Handles language switching and translations
 * Supports: English (en), French (fr), Spanish (es), Arabic (ar)
 * 
 * @package TelecomWebsite
 * @version 2.0.0
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get current language
 * 
 * @return string Language code (en, fr, es, ar)
 */
function getCurrentLanguage()
{
    // Check if language is set in session
    if (isset($_SESSION['language'])) {
        return $_SESSION['language'];
    }

    // Check if language is set in cookie
    if (isset($_COOKIE['language'])) {
        return $_COOKIE['language'];
    }

    // Default to English
    return 'en';
}

/**
 * Set current language
 * 
 * @param string $lang Language code
 */
function setLanguage($lang)
{
    $supported = ['en', 'fr', 'es', 'ar'];

    if (in_array($lang, $supported)) {
        $_SESSION['language'] = $lang;
        setcookie('language', $lang, time() + (86400 * 365), '/'); // 1 year
        return true;
    }

    return false;
}

/**
 * Load translations for current language
 * 
 * @return array Translations
 */
function loadTranslations()
{
    $lang = getCurrentLanguage();
    $file = __DIR__ . "/../languages/{$lang}.php";

    if (file_exists($file)) {
        return require $file;
    }

    // Fallback to English
    return require __DIR__ . "/../languages/en.php";
}

/**
 * Get translation
 * 
 * @param string $key Translation key
 * @param array $params Parameters for sprintf
 * @return string Translated text
 */
function t($key, $params = [])
{
    static $translations = null;

    if ($translations === null) {
        $translations = loadTranslations();
    }

    // Navigate nested keys (e.g., 'nav.home')
    $keys = explode('.', $key);
    $value = $translations;

    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $key; // Return key if translation not found
        }
    }

    // Apply parameters if provided
    if (!empty($params) && is_string($value)) {
        return vsprintf($value, $params);
    }

    return $value;
}

/**
 * Get language direction (LTR or RTL)
 * 
 * @return string 'ltr' or 'rtl'
 */
function getLanguageDirection()
{
    $lang = getCurrentLanguage();
    return $lang === 'ar' ? 'rtl' : 'ltr';
}

/**
 * Get available languages
 * 
 * @return array Language codes and names
 */
function getAvailableLanguages()
{
    return [
        'en' => 'English',
        'fr' => 'Français',
        'es' => 'Español',
        'ar' => 'العربية'
    ];
}

// Handle language switch
if (isset($_GET['lang'])) {
    setLanguage($_GET['lang']);

    // Redirect to remove lang parameter
    $redirect_url = strtok($_SERVER['REQUEST_URI'], '?');
    header('Location: ' . $redirect_url);
    exit;
}
