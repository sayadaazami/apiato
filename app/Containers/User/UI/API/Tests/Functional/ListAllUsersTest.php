<?php

namespace App\Containers\User\UI\API\Tests\Functional;

use App\Containers\User\Models\User;
use App\Port\Test\PHPUnit\Abstracts\TestCase;

/**
 * Class ListAllUsersTest.
 *
 * @author  Mahmoud Zalt <mahmoud@zalt.me>
 */
class ListAllUsersTest extends TestCase
{

    private $endpoint = '/users';

    public $permissions = [
        'list-users'
    ];

    public function testListAllUsersByAdmin_()
    {
        $admin = $this->getLoggedInTestingAdmin();

        // create some non-admin users
        factory(User::class, 4)->create();

        // send the HTTP request
        $response = $this->apiCall($this->endpoint, 'get');

        // assert response status is correct
        $this->assertEquals($response->getStatusCode(), '200');

        // convert JSON response string to Object
        $responseObject = $this->getResponseObject($response);

        // assert the returned data size is correct
        $this->assertCount(6,
            $responseObject->data); // 6 = 4 (fake in this test) + 1 (that is logged in) + 1 (seeded super admin)
    }

    public function testListAllUsersByNonAdmin_()
    {
        // by default permission is set, so we need to revoke it manually
        $this->getLoggedInTestingUser()->revokePermissionTo($this->permissions);

        // create some fake users
        factory(User::class, 4)->create();

        // send the HTTP request
        $response = $this->apiCall($this->endpoint, 'get');

        // assert response status is correct
        $this->assertEquals($response->getStatusCode(), '403');
    }

}
