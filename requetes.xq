<results>

<Q1_membres>
{
  for $m in db:open("Club")//membre
    let $cat := db:open("Club")//categorie[@id = $m/@categorieRef]
  return
    <membre id="{$m/@id}">
      <nomComplet>{$m/prenom/text()} {$m/nom/text()}</nomComplet>
      <email>{$m/email/text()}</email>
      <categorie>{$cat/@libelle/string()}</categorie>
    </membre>
}
</Q1_membres>

<Q2_concours_tries>
{
  for $c in db:open("Club")//concours[@id]
    let $cat := db:open("Club")//categorie[@id = $c/@categorieRef]
  order by $c/@date ascending
  return
    <concours>
      <titre>{$c/titre/text()}</titre>
      <date>{$c/@date/string()}</date>
      <coefficient>{$c/@coefficient/string()}</coefficient>
      <categorie>{$cat/@libelle/string()}</categorie>
    </concours>
}
</Q2_concours_tries>

<Q3_scores>
{
  for $c in db:open("Club")//concours[@id]
    let $coeff := xs:decimal($c/@coefficient)
  return
    <concours titre="{$c/titre/text()}">
    {
      for $p in $c//participant
        let $m     := db:open("Club")//membre[@id = $p/@membreRef]
        let $score := ($p/complexite + $p/tempsExecution) * $coeff
      return
        <participant>
          <nom>{$m/prenom/text()} {$m/nom/text()}</nom>
          <complexite>{$p/complexite/text()}</complexite>
          <tempsExecution>{$p/tempsExecution/text()}</tempsExecution>
          <score>{format-number($score, '#.##')}</score>
        </participant>
    }
    </concours>
}
</Q3_scores>

<Q4_vainqueurs>
{
  for $c in db:open("Club")//concours[@id]
    let $coeff := xs:decimal($c/@coefficient)
    let $scores :=
      for $p in $c//participant
      return ($p/complexite + $p/tempsExecution) * $coeff
    let $maxScore := max($scores)
  return
    <concours titre="{$c/titre/text()}">
    {
      for $p in $c//participant
        let $m     := db:open("Club")//membre[@id = $p/@membreRef]
        let $score := ($p/complexite + $p/tempsExecution) * $coeff
      where $score = $maxScore
      return
        <vainqueur>
          <nom>{$m/prenom/text()} {$m/nom/text()}</nom>
          <score>{format-number($score, '#.##')}</score>
        </vainqueur>
    }
    </concours>
}
</Q4_vainqueurs>

{
  let $categorie := "Intelligence Artificielle"
  let $catId := db:open("Club")//categorie[@libelle = $categorie]/@id
  return
    <Q5_membres_categorie nom="{$categorie}">
    {
      for $m in db:open("Club")//membre[@categorieRef = $catId]
      order by $m/nom ascending, $m/prenom ascending
      return
        <membre id="{$m/@id}">
          <nom>{$m/nom/text()}</nom>
          <prenom>{$m/prenom/text()}</prenom>
          <email>{$m/email/text()}</email>
        </membre>
    }
    </Q5_membres_categorie>
}

</results>