<?php namespace Pz\Doctrine\Rest;

use Pz\Doctrine\Rest\Contracts\RestRequestContract;
use Pz\Doctrine\Rest\Exceptions\RestException;
use Symfony\Component\HttpFoundation\Request;

class RestRequest implements RestRequestContract
{
    /**
     * @var Request
     */
    protected $http;

    /**
     * RestRequest constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->http = $request;
    }

    /**
     * @return Request
     */
    public function http()
    {
        return $this->http;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->http()->getBaseUrl();
    }

    /**
     * @return int|array
     */
    public function getId()
    {
        return $this->http()->get('id');
    }

    /**
     * @return array
     * @throws RestException
     */
    public function getData()
    {
        $request = $this->http()->request;

        if ($request->has('data')) {
            return $request->get('data');
        }

        throw RestException::missingRootData();
    }

    /**
     * Get jsonapi fieldsets.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->http()->get('fields');
    }

    /**
     * @return mixed|null
     */
    public function getFilter()
    {
        if ($query = $this->http()->get('filter')) {
            if (is_string($query) && (null !== ($json = json_decode($query, true)))) {
                return $json;
            }

            return $query;
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getOrderBy()
    {
        if ($sort = $this->http()->get('sort')) {
            $fields = explode(',', $sort);
            $orderBy = [];

            foreach ($fields as $field) {
                $direction = 'ASC';
                if ($field[0] === '-') {
                    $field = substr($field, 1);
                    $direction = 'DESC';
                }

                $orderBy[$field] = $direction;
            }

            return $orderBy;
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getStart()
    {
        if (($page = $this->http()->get('page')) && $this->getLimit() !== null) {
            if (isset($page['number']) && is_numeric($page['number'])) {
                return ($page['number'] - 1) * $this->getLimit();
            }

            return isset($page['offset']) && is_numeric($page['offset']) ? (int) $page['offset'] : 0;
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        if ($page = $this->http()->get('page')) {
            if (isset($page['number']) && is_numeric($page['number'])) {
                return isset($page['size']) && is_numeric($page['size']) ?
                    (int) $page['size'] : static::DEFAULT_LIMIT;
            }

            return isset($page['limit']) && is_numeric($page['limit']) ?
                (int) $page['limit'] : static::DEFAULT_LIMIT;
        }

        return null;
    }

    /**
     * @return array|string|null
     */
    public function getInclude()
    {
        return $this->http()->get('include');
    }

    /**
     * @return array|string|null
     */
    public function getExclude()
    {
        return $this->http()->get('exclude');
    }
}
