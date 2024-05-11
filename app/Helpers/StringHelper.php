<?php

if (!function_exists('removeRomanianDiacritics')) {
    function removeRomanianDiacritics($string) {
        // Definim un array asociativ cu caracterele din limba română și echivalentele lor fără diacritice
        $diacriticsMap = array(
            'ă' => 'a', 'Ă' => 'A',
            'î' => 'i', 'Î' => 'I',
            'â' => 'a', 'Â' => 'A',
            'ș' => 's', 'Ș' => 'S',
            'ț' => 't', 'Ț' => 'T',
            'ş' => 's', 'Ş' => 'S'
            // Puteți adăuga orice alte caractere specifice limbii române și echivalentele lor fără diacritice aici
        );

        // Înlocuim diacriticele cu echivalentele lor fără diacritice din array
        $string = strtr($string, $diacriticsMap);

        return $string;
    }
}

if (!function_exists('calculateSimilarityStringsPercentage')) {
    function calculateSimilarityStringsPercentage($string1, $string2) {
        $string1 = removeRomanianDiacritics($string1);
        $string2 = removeRomanianDiacritics($string2);
        // Eliminăm spațiile din ambele șiruri și le convertim în litere mici
        $string1 = strtolower(str_replace(' ', '', $string1));
        $string2 = strtolower(str_replace(' ', '', $string2));
    
        // Calculăm similitatea între cele două șiruri folosind similar_text
        similar_text($string1, $string2, $similarity);
    
        // Rezultatul similar_text este stocat în variabila $similarity ca procent de similitudine
        return $similarity;
    }    
}


