
# SellAssist

SellAssist to projekt zbudowany w PHP.

## Jak uruchomić projekt?

Aby uruchomić projekt lokalnie, wykonaj poniższe kroki:

1. Sklonuj repozytorium oraz przejdź do katalogu projektu:
   ```bash
   git clone https://github.com/matinosal/sellasist.git
   cd sellasist
   ```

2. Skopiuj plik `.env.dist` do `.env`:
   ```bash
   cp .env.dist .env
   ```

3. Zbuduj obrazy Dockera:
   ```bash
   docker compose build --pull --no-cache
   ```

4. Uruchom kontenery:
   ```bash
   docker compose up --wait
   ```

5. Projekt powinien teraz działać. Otwórz przeglądarkę i przejdź na adres `https://localhost:9001` (HTTPS preferowany)

---

## Panel dokumentacji

Po uruchomieniu aplikacji panel dokumentacji API jest dostępny pod ścieżką `/api/docs`. Możesz otworzyć przeglądarkę i przejść do:

```
https://localhost:9001/api/docs
```

W panelu znajdziesz szczegółowe informacje o endpointach dostępnych w projekcie, w tym metody HTTP, wymagane parametry i odpowiedzi.

---

## Jak uruchomić testy PHP?

W projekcie znajdują się testy napisane w PHP (np. za pomocą PHPUnit). Aby je uruchomić, wykonaj poniższe kroki:

1. Otwórz terminal w katalogu głównym projektu (na węźle Dockera, jeśli wymagane).

2. Uruchom testy za pomocą następującego polecenia:
   ```bash
   docker compose exec sellasist-matinosal vendor/bin/phpunit
   ```

## Technologie użyte w projekcie

W projekcie wykorzystano następujące technologie:

- **PHP 8.4**: podstawowy język użyty do budowy logiki projektu.
- **Symfony 8.0**: podstawowy język użyty do budowy logiki projektu.
- **Docker** (pliki `Dockerfile` i `docker-compose.yml`): narzędzie do konteneryzacji aplikacji, umożliwia łatwe uruchomienie i utrzymanie środowiska.

---

