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

#[Route('/api/country')]
class CountryController extends AbstractController
{
    private CountryScenarios $countryScenarios;

    public function __construct(CountryScenarios $countryScenarios)
    {
        $this->countryScenarios = $countryScenarios;
    }

    #[Route('', name: 'get_all_countries', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        try {
            $countries = $this->countryScenarios->getAll();
            $result = array_map(function (Country $country) {
                return $country->toArray();
            }, $countries);
            
            return new JsonResponse($result, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->errorResponse('Server error: ' . $e->getMessage(), 500);
        }
    }

    #[Route('/{code}', name: 'get_country', methods: ['GET'])]
    public function get(string $code): JsonResponse
    {
        try {
            $country = $this->countryScenarios->get($code);
            return new JsonResponse($country->toArray(), JsonResponse::HTTP_OK);
        } catch (InvalidCountryCodeException $e) {
            return $this->errorResponse('Invalid country code format', JsonResponse::HTTP_BAD_REQUEST);
        } catch (CountryNotFoundException $e) {
            return $this->errorResponse('Country not found', JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->errorResponse('Server error: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'create_country', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (empty($data)) {
                return $this->errorResponse('Invalid JSON format', JsonResponse::HTTP_BAD_REQUEST);
            }

            $country = Country::fromArray($data);
            $this->countryScenarios->store($country);
            
            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation error: ' . $e->getMessage(), JsonResponse::HTTP_BAD_REQUEST);
        } catch (DuplicateCountryException $e) {
            return $this->errorResponse('Conflict: ' . $e->getMessage(), JsonResponse::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return $this->errorResponse('Server error: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{code}', name: 'update_country', methods: ['PATCH'])]
    public function update(string $code, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (empty($data)) {
                return $this->errorResponse('Invalid JSON format', JsonResponse::HTTP_BAD_REQUEST);
            }

            $country = Country::fromArray($data);
            $updatedCountry = $this->countryScenarios->edit($code, $country);
            
            return new JsonResponse($updatedCountry->toArray(), JsonResponse::HTTP_OK);
        } catch (InvalidCountryCodeException $e) {
            return $this->errorResponse('Invalid country code format', JsonResponse::HTTP_BAD_REQUEST);
        } catch (CountryNotFoundException $e) {
            return $this->errorResponse('Country not found', JsonResponse::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation error: ' . $e->getMessage(), JsonResponse::HTTP_BAD_REQUEST);
        } catch (DuplicateCountryException $e) {
            return $this->errorResponse('Conflict: ' . $e->getMessage(), JsonResponse::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return $this->errorResponse('Server error: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{code}', name: 'delete_country', methods: ['DELETE'])]
    public function delete(string $code): JsonResponse
    {
        try {
            $this->countryScenarios->delete($code);
            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (InvalidCountryCodeException $e) {
            return $this->errorResponse('Invalid country code format', JsonResponse::HTTP_BAD_REQUEST);
        } catch (CountryNotFoundException $e) {
            return $this->errorResponse('Country not found', JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->errorResponse('Server error: ' . $e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function errorResponse(string $message, int $statusCode): JsonResponse
    {
        return new JsonResponse([
            'error' => $message,
        ], $statusCode);
    }
}
