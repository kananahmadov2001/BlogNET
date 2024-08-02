
<?php  
    // meant to be packaged and used by other files
    // relevant methodology: https://stackoverflow.com/questions/273516/how-do-you-implement-a-good-profanity-filter 
    function moderate_word($input) {
        
        $bad_words = ["fuck", "shit", "bitch", "asshole", "dick", "cunt", "coochie", "pussy", "whore", 
        "hoe", "slut", "retard", "midget", "mierda", "joder", "caca", "verga"];

        $censored_words = [];

        foreach ($input as $word) {
            if (in_array(strtolower($word), $bad_words)) {
                $censored_words[] = str_repeat('*', strlen($word));
            } else {
                $censored_words[] = $word;
            }
        }
        return implode(" ", $censored_words);
    }
        
?>