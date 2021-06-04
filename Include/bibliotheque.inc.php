<?php

/**
 * Créer une liste en passant les paramêtres obligatoire suivant :
 * @param string $label Le texte du libellé
 * @param string $name Le nom du select (utile seulement du côté serveur)
 * @param string $id L'identifiant du select
 * @param int $tabIndex L'indice de tabulation du select
 * @param object $jeuEnregistrement Le jeu d'enregistrement permettant de faire apparaître les informations d'une base de données
 * @param bool $disabled Désactiver ou pas la liste
 * @param bool $selected Indique si le composant est sélectionner par défaut (falcultatif)
 * @return string Le code HTML du select avec les informations demandé
 */
function formSelectDepuisRecordset($label, $name, $id, $tabIndex, $jeuEnregistrement, $disabled, $selected = null) {
    $ligne = $jeuEnregistrement->fetch(PDO::FETCH_NUM);
    $options = '';

    while ($ligne) {
        if ($selected === null) {
            $options .= '<option value="' . $ligne[0] . '">' . $ligne[1] . '</option>' . "\n";
            $ligne = $jeuEnregistrement->fetch();
        } else {
            $options .= '<option ' . ($ligne[0] == $selected ? 'selected="selected"' : '') . ' value="' . $ligne[0] . '">' . $ligne[1] . '</option>' . "\n";
            $ligne = $jeuEnregistrement->fetch();
        }
    }

    return ($label != '' ? '<label class="titre" for="' . $id . '">' . $label . ' :</label>' . "\n" : '')
            . '<select class="zone" name="' . $name . '" id="' . $id . '" tabindex="' . $tabIndex . '"' . ($disabled ? ' disabled="disabled"' : '') . '>' . "\n"
            . $options
            . '</select>' . "\n";
}

/**
 * Créer une liste en passant les paramêtres obligatoire suivant :
 * @param string $label Le texte du libellé
 * @param string $name Le nom du select (utile seulement du côté serveur)
 * @param string $id $id L'identifiant du select
 * @param int $tabIndex L'indice de tabulation du select
 * @param array $collection2D Un tableau de 2 dimension
 * @param bool $disabled Désactiver ou pas la liste
 * @param bool $selected Indique si le composant est sélectionner par défaut (falcultatif)
 * @return string Le code HTML du select avec les informations demandé
 */
function formSelectDepuisTab2D($label, $name, $id, $tabIndex, $collection2D, $disabled, $selected = null) {
    $options = '';

    for ($i = 0; $i < count($collection2D); $i++) {
        if ($selected === null) {
            $options .= '<option value="' . $collection2D[$i][0] . '">' . $collection2D[$i][1] . '</option>' . "\n";
        } else {
            $options .= '<option value="' . $collection2D[$i][0] . '" ' . ($collection2D[$i][0] == $selected ? 'selected="selected"' : '') . '>' . $collection2D[$i][1] . '</option>' . "\n";
        }
    }

    return ($label != '' ? '<label class="titre" for="' . $id . '">' . $label . ' :</label>' . "\n" : '')
            . '<select class="zone" name="' . $name . '" id="' . $id . '" tabindex="' . $tabIndex . '"' . ($disabled ? ' disabled="disabled"' : '') . '>' . "\n"
            . $options
            . '</select><br /><br />' . "\n";
}

/**
 * Créer une de saisie de mot de passe en passant en paramêtres obligatoire suivant :
 * @param string $label Le texte du libellé
 * @param string $name Le nom de la saisie (utilise seulement pour le côté serveur)
 * @param string $id L'identifiant de la saisie
 * @param string $value La valeur de la saisie
 * @param int $size La taille de la saisie
 * @param int $maxLength La taille de caractère que peut accepté la saisie
 * @param int $tabIndex L'indice de tabulation de la saisie
 * @param bool $readonly Indique si la saisie est seulement en lecture seul
 * @param bool $required Indique si la saisie est obligatoire à renseigner
 * @return string Le code HTML de la saisie avec les informations demandé
 */
function formInputPassword($label, $name, $id, $value, $size, $maxLength, $tabIndex, $readonly, $required) {
    return '<label class="titre" for="' . $id . '">' . $label . ' :</label>' . "\n"
            . '<input type="password" class="zone" name="' . $name . '" id="' . $id . '" value="' . $value . '" size="' . $size . '" maxlength="' . $maxLength . '" tabindex="' . $tabIndex . '" ' . ($readonly ? 'readonly="readonly"' : '') . ' ' . ($required ? 'required="required"' : '') . '><br /><br />' . "\n";
}

/**
 * Créer une de saisie de texte simple en passant en paramêtres obligatoire suivant :
 * @param string $label Le texte du libellé
 * @param string $name Le nom de la saisie (utilise seulement pour le côté serveur)
 * @param string $id L'identifiant de la saisie
 * @param string $value La valeur de la saisie
 * @param int $size La taille de la saisie
 * @param int $maxLength La taille de caractère que peut accepté la saisie
 * @param int $tabIndex L'indice de tabulation de la saisie
 * @param bool $readonly Indique si la saisie est seulement en lecture seul
 * @param bool $required Indique si la saisie est obligatoire à renseigner
 * @return string Le code HTML de la saisie avec les informations demandé
 */
function formInputText($label, $name, $id, $value, $size, $maxLength, $tabIndex, $readonly, $required) {
    return ($label != '' ? '<label class="titre" for="' . $id . '">' . $label . ' :</label>' . "\n" : '')
            . '<input type="text" class="zone" name="' . $name . '" id="' . $id . '" value="' . $value . '" size="' . $size . '" maxlength="' . $maxLength . '" tabindex="' . $tabIndex . '" ' . ($readonly ? 'readonly="readonly"' : '') . ' ' . ($required ? 'required="required"' : '') . '>' . "\n";
}

/**
 * Créer un bouton de soumission de formulaire en passant en paramêtres obligatoire suivant :
 * @param string $name Le nom du bouton (utilise seulement pour le côté serveur)
 * @param string $îd L'identifiant du bouton
 * @param string $value Le texte du bouton mais aussi sa valeur
 * @param int $tabindex L'indice de tabulation du bouton
 * @param bool $readonly Indique si le bouton est en lecture seul
 * @return string Le code HTML du bouton avec les informations demandé
 */
function formBoutonSubmit($name, $id, $value, $tabindex, $disabled = false) {
    return '<input type="submit" name="' . $name . '" id="' . $id . '" value="' . $value . '" tabindex="' . $tabindex . '"' . ($disabled ? ' disabled="disabled"' : '') . '>' . "\n";
}

/**
 * Créer une champ caché en passant en paramêtres obligatoire suivant :
 * @param string $name Le nom du champ caché (utilise seulement pour le côté serveur)
 * @param string $id L'identifiant du champ caché
 * @param string $value La valeur du champ caché
 * @return string Le code HTML du champ caché avec les informations demandé
 */
function formInputHidden($name, $id, $value) {
    return '<input type="hidden" name="' . $name . '" id="' . $id . '" value="' . $value . '">' . "\n";
}

/**
 * Créer une de saisie de texte agrandi en passant en paramêtres obligatoire suivant :
 * @param string $label Le texte du libellé
 * @param string $name Le nom de la saisie (utilise seulement pour le côté serveur)
 * @param string $id L'identifiant de la saisie
 * @param string $value La valeur de la saisie
 * @param int $cols Le nombre de colonne de le saisie
 * @param int $rows le nombre de ligne de la saisie
 * @param int $maxLength La taille de caractère que peut accepté la saisie
 * @param int $tabIndex L'indice de tabulation de la saisie
 * @param bool $readonly Indique si la saisie est seulement en lecture seul
 * @return string Le code HTML de la saisie avec les informations demandé
 */
function formTextArea($label, $name, $id, $value, $cols, $rows, $maxLength, $tabIndex, $readonly) {
    return '<label class="titre" for="' . $id . '">' . $label . ' :</label>' . "\n"
            . '<textarea class="zone" name="' . $name . '" id="' . $id . '" cols="' . $cols . '" rows="' . $rows . '" maxlength="' . $maxLength . '" tabindex="' . $tabIndex . '" ' . ($readonly ? 'readonly="readonly"' : '') . '>' . "\n"
            . $value . "\n"
            . '</textarea>' . "\n";
}

/**
 * Créer une saisie de case à cocher en passant en paramêtres obligatoire suivant :
 * @param bool $beforeLabel Positionner le texte avant le libelle
 * @param string $label Le texte du libellé
 * @param string $name Le nom de la saisie (utilise seulement pour le côté serveur)
 * @param string $id L'identifiant de la saisie
 * @param bool $checked Indique si la saisie doit être cocher par défaut
 * @param int $tabIndex L'indice de tabulation de la saisie
 * @param bool $disabled Indique si la saisie est seulement en lecture seul (readonly ne fonctionne pas)
 * @return string Le code HTML de la saisie avec les informations demandé
 */
function formInputCheckBox($beforeLabel, $label, $name, $id, $checked, $tabIndex, $disabled) {
    return (!$beforeLabel ? '<label class="titre" for="' . $id . '">' . $label . ' :</label>' . "\n" : '')
            . '<input type="checkbox" class="zone" name="' . $name . '" id="' . $id . '" tabIndex="' . $tabIndex . '"' . ($checked ? ' checked="checked"' : '') . ($disabled ? ' disabled="disabled"' : '') . '>' . "\n"
            . ($beforeLabel ? '<label class="titre" for="' . $id . '">' . $label . ' :</label>' . "\n" : '');
}

/**
 * Créer une saisie de bouton radio en passant en paramêtres obligatoire suivant :
 * @param bool $beforeLabel Positionner le texte avant le libelle
 * @param string $label Le texte du libellé
 * @param string $name Le nom de la saisie (utilise seulement pour le côté serveur)
 * @param string $id L'identifiant de la saisie
 * @param string $value La valeur de la saisie
 * @param bool $checked Indique si la saisie doit être cocher par défaut
 * @param int $tabIndex L'indice de tabulation de la saisie
 * @param bool $disabled Indique si la saisie est seulement en lecture seul (readonly ne fonctionne pas)
 * @return string Le code HTML de la saisie avec les informations demandé
 */
function formInputRadio($beforeLabel, $label, $name, $id, $value, $checked, $tabIndex, $disabled) {
    return (!$beforeLabel ? '<label class="titre" for="' . $id . '">' . $label . '</label>' . "\n" : '')
            . '<input type="radio" class="zone" value="' . $value . '" name="' . $name . '" id="' . $id . '" tabIndex="' . $tabIndex . '"' . ($checked ? ' checked="checked"' : '') . ($disabled ? ' disabled="disabled"' : '') . '>' . "\n"
            . ($beforeLabel ? '<label class="titre" for="' . $id . '">' . $label . ' :</label>' . "\n" : '');
}

/**
 * Créer une saisie de nombre en passant en paramêtres obligatoire suivant :
 * @param string $label Le texte du libellé
 * @param string $name Le nom de la saisie (utilise seulement pour le côté serveur)
 * @param string $id L'identifiant de la saisie
 * @param int $minValue Valeur minimum dont les nombres peuvent aller
 * @param int $maxValue Valeur maximum dont les nombres peuvent aller
 * @param int $step Nombre de pas de saisie
 * @param int $tabIndex L'indice de tabulation de la saisie
 * @param bool $readonly Indique si la saisie est seulement en lecture seul
 * @param bool $required Indique si la saisie est obligatoire à renseigner
 * @param string $placeholder Le texte indicatif
 * @return string Le code HTML de la saisie avec les informations demandé
 */
function formInputNumber($label, $name, $id, $minValue, $maxValue, $step, $tabIndex, $readonly, $required, $placeholder) {
    return '<label class="titre" for="' . $id . '">' . $label . ' :</label>' . "\n"
            . '<input type="number" class="zone" name="' . $name . '" id="' . $id . '" min="' . $minValue . '" max="' . $maxValue . '" step="' . $step . '" tabIndex="' . $tabIndex . '"' . ($readonly ? ' readonly="readonly"' : '') . ($required ? ' required="required"' : '') . ' placeholder="' . $placeholder . '">' . "\n";
}

/**
 * Créer un bouton de reinitialisation du formulaire en passant en paramêtres obligatoire suivant :
 * @param string $name Le nom du bouton (utilise seulement pour le côté serveur)
 * @param string $îd L'identifiant du bouton
 * @param string $value Le texte du bouton mais aussi sa valeur
 * @param int $tabindex L'indice de tabulation du bouton
 * @param bool $disabled Indique si le bouton est en lecture seul
 * @return string Le code HTML du bouton avec les informations demandé
 */
function formBoutonReset($name, $îd, $value, $tabindex, $disabled = false) {
    return '<input type="submit" name="' . $name . '" id="' . $îd . '" value="' . $value . '" tabindex="' . $tabindex . '"' . ($disabled ? ' disabled="disabled"' : '') . '>' . "\n";
}

?>