/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-01-17 14:16
 */

export const debug = state => state.debug;

export const setting = state => state.setting;

export const token = state => state.token;

export const accessToken = state => state.token.access_token;

export const tokenExpiresIn = state => state.token.expires_in;

export const refreshToken = state => state.token.refresh_token;