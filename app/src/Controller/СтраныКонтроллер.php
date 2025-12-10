<?php

namespace App\Controller;

use App\Model\Country;
use App\Model\CountryScenarios;
use App\Model\Exceptions\CountryNotFoundException;
use App\Model\Exceptions\InvalidCountryCodeException;
use App\Model\Exceptions\ValidationException;
use App\Model\Exceptions\DuplicateCountryException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/страна')]
class СтраныКонтроллер extends AbstractController
{
    private CountryScenarios $countryScenarios;

    public function __construct(CountryScenarios $countryScenarios)
    {
        $this->countryScenarios = $countryScenarios;
    }

    #[Route('', name: 'все_страны', methods: ['GET'])]
    public function получитьВсеСтраны(): JsonResponse
    {
        try {
            $страны = $this->countryScenarios->getAll();
            $результат = array_map(function (Country $страна) {
                return $страна->toArray();
            }, $страны);
            
            return new JsonResponse($результат, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->ошибкаОтвет('Ошибка при получении стран', 500);
        }
    }

    #[Route('/{код}', name: 'получить_страну', methods: ['GET'])]
    public function получитьСтрану(string $код): JsonResponse
    {
        try {
            $страна = $this->countryScenarios->get($код);
            return new JsonResponse($страна->toArray(), JsonResponse::HTTP_OK);
        } catch (InvalidCountryCodeException $e) {
            return $this->ошибкаОтвет('Невалидный код страны', JsonResponse::HTTP_BAD_REQUEST);
        } catch (CountryNotFoundException $e) {
            return $this->ошибкаОтвет('Страна не найдена', JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->ошибкаОтвет('Ошибка сервера', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'создать_страну', methods: ['POST'])]
    public function создатьСтрану(Request $request): JsonResponse
    {
        try {
            $данные = json_decode($request->getContent(), true);
            
            if (empty($данные)) {
                return $this->ошибкаОтвет('Некорректный JSON', JsonResponse::HTTP_BAD_REQUEST);
            }

            $страна = Country::fromArray($данные);
            $this->countryScenarios->store($страна);
            
            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (ValidationException $e) {
            return $this->ошибкаОтвет('Ошибка валидации: ' . $e->getMessage(), JsonResponse::HTTP_BAD_REQUEST);
        } catch (DuplicateCountryException $e) {
            return $this->ошибкаОтвет('Конфликт: ' . $e->getMessage(), JsonResponse::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return $this->ошибкаОтвет('Ошибка при создании страны: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{код}', name: 'обновить_страну', methods: ['PATCH'])]
    public function обновитьСтрану(string $код, Request $request): JsonResponse
    {
        try {
            $данные = json_decode($request->getContent(), true);
            
            if (empty($данные)) {
                return $this->ошибкаОтвет('Некорректный JSON', JsonResponse::HTTP_BAD_REQUEST);
            }

            $страна = Country::fromArray($данные);
            $обновлённаяСтрана = $this->countryScenarios->edit($код, $страна);
            
            return new JsonResponse($обновлённаяСтрана->toArray(), JsonResponse::HTTP_OK);
        } catch (InvalidCountryCodeException $e) {
            return $this->ошибкаОтвет('Невалидный код страны', JsonResponse::HTTP_BAD_REQUEST);
        } catch (CountryNotFoundException $e) {
            return $this->ошибкаОтвет('Страна не найдена', JsonResponse::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return $this->ошибкаОтвет('Ошибка валидации: ' . $e->getMessage(), JsonResponse::HTTP_BAD_REQUEST);
        } catch (DuplicateCountryException $e) {
            return $this->ошибкаОтвет('Конфликт: ' . $e->getMessage(), JsonResponse::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return $this->ошибкаОтвет('Ошибка при обновлении: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{код}', name: 'удалить_страну', methods: ['DELETE'])]
    public function удалитьСтрану(string $код): JsonResponse
    {
        try {
            $this->countryScenarios->delete($код);
            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (InvalidCountryCodeException $e) {
            return $this->ошибкаОтвет('Невалидный код страны', JsonResponse::HTTP_BAD_REQUEST);
        } catch (CountryNotFoundException $e) {
            return $this->ошибкаОтвет('Страна не найдена', JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->ошибкаОтвет('Ошибка при удалении: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function ошибкаОтвет(string $сообщение, int $статусКод): JsonResponse
    {
        return new JsonResponse([
            'ошибка' => $сообщение,
        ], $статусКод);
    }
}
