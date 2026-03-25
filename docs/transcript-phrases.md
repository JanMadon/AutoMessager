# Frazy z transkrypcji (OpenAI) — jak to działa

Ten dokument opisuje funkcję analizy transkrypcji serialu i zapisu fraz do nauki angielskiego.

## Cel funkcji

Użytkownik:
- podaje poziom angielskiego (`A1`–`C2`),
- wkleja transkrypcję **lub** wrzuca plik (`.txt`, `.srt`, `.vtt`),
- uruchamia analizę.

Aplikacja:
- czyści tekst (szczególnie napisy SRT/VTT),
- wysyła go do OpenAI,
- odbiera listę fraz z tłumaczeniem i kontekstem,
- zapisuje wyniki w bazie danych,
- pokazuje ostatnio zapisane frazy w zakładce.

---

## Główne elementy

### 1) Strona Filament

Plik: `app/Filament/Pages/TranscriptPhrasesPage.php`

Odpowiada za:
- walidację danych wejściowych,
- pobranie treści transkrypcji (tekst lub plik),
- czyszczenie napisów,
- wywołanie serwisu OpenAI,
- zapis wyników do tabeli `learning_phrases`.

Najważniejsze pola Livewire:
- `$episodeTitle` — opcjonalny tytuł odcinka,
- `$englishLevel` — poziom użytkownika,
- `$transcript` — ręcznie wklejony tekst,
- `$transcriptFile` — upload pliku,
- `$phrases` — lista ostatnich zapisanych rekordów.

### 2) Widok zakładki

Plik: `resources/views/filament/pages/transcript-phrases-page.blade.php`

Zawiera:
- formularz (tytuł, poziom, plik, textarea),
- przycisk analizy,
- listę ostatnio zapisanych fraz.

### 3) Serwis OpenAI

Plik: `app/Services/OpenAIService.php`

Zawiera:
- `extractLearningPhrases(string $transcript, string $englishLevel): array` — prompt do OpenAI,
- `parseLearningPhrases(string $responseText): array` — bezpieczne parsowanie odpowiedzi JSON.

Zasady doboru fraz w promptcie:
- frazy mają być ponad poziom użytkownika (lub na granicy poziomu),
- muszą być istotne do zrozumienia **konkretnego** odcinka,
- liczba fraz jest dynamiczna (zależna od trudności transkrypcji, zwykle `5-25`),
- model nie powinien „dopchać” listy na siłę,
- preferowane są idiomy, phrasal verbs i potoczne formy blokujące zrozumienie.

Wymuszana struktura odpowiedzi:

```json
{
  "phrases": [
    {
      "phrase": "...",
      "translation": "...",
      "context_sentence": "...",
      "explanation": "..."
    }
  ]
}
```

Jeśli model zwróci błędny format, aplikacja pokaże komunikat i nic nie zapisze.

### 4) Model i baza danych

Model: `app/Models/LearningPhrase.php`  
Migracja: `database/migrations/2026_03_25_171900_create_learning_phrases_table.php`

Tabela `learning_phrases` przechowuje:
- `user_id`,
- `episode_title`,
- `english_level`,
- `phrase`,
- `translation`,
- `context_sentence`,
- `explanation`,
- `timestamps`.

---

## Przepływ krok po kroku

1. Użytkownik otwiera zakładkę **Frazy z transkrypcji**.
2. Uzupełnia poziom i dodaje tekst lub plik.
3. `analyzeTranscript()` waliduje input.
4. Aplikacja składa transkrypcję:
   - priorytet ma pole tekstowe,
   - jeśli puste, używany jest upload pliku.
5. Dla pliku uruchamiane jest czyszczenie napisów (`normalizeSubtitleText()`).
6. Tekst trafia do `OpenAIService::extractLearningPhrases()`.
7. Odpowiedź JSON jest parsowana i filtrowana.
8. Każda poprawna fraza zapisywana jest jako osobny rekord `LearningPhrase`.
9. Użytkownik dostaje notyfikację sukcesu i widzi odświeżoną listę fraz.

---

## Czyszczenie napisów (SRT/VTT)

Parser czyści napisy blokowo (cue-by-cue), żeby do modelu szedł możliwie „czysty dialog”.

Usuwane są m.in.:
- numeracja bloków (`1`, `2`, `3`...),
- timestampy (`00:00:00,000 --> 00:00:02,000`),
- nagłówki (`WEBVTT`, `NOTE`, `Kind:`, `Language:`),
- tagi i style (`<i>`, `{\an8}`),
- opisy efektów (`[music]`, `(whispers)`),
- nadmiarowe spacje i puste linie.

Dodatkowo linie dialogu z jednego bloku są łączone w jedno zdanie.

---

## Walidacja i limity

- Poziom: tylko `A1`, `A2`, `B1`, `B2`, `C1`, `C2`.
- Upload: `txt/srt/vtt`, max `5 MB`.
- Minimalna długość finalnej transkrypcji: `50` znaków.
- Wymagany zalogowany użytkownik (`user_id` przypisany do rekordów).

---

## Konfiguracja

Wymagane env:

- `OPENAI_API_KEY`

Plik: `.env`  
Przykład wpisu jest też w `.env.example`.

---

## Jak uruchomić

1. Ustaw `OPENAI_API_KEY` w `.env`.
2. Uruchom migracje:

```bash
php artisan migrate
```

(jeśli używasz dockera w tym projekcie, możesz użyć `docker/scripts/artisan.sh migrate`)

3. Zaloguj się do panelu Filament i przejdź do zakładki **Frazy z transkrypcji**.

---

## Znane ograniczenia

- Jakość wyniku zależy od jakości transkrypcji i odpowiedzi modelu.
- Bardzo zaszumione napisy mogą wymagać dalszego dopracowania parsera.
- Obecnie frazy zapisują się bez deduplikacji (mogą pojawić się powtórki).
