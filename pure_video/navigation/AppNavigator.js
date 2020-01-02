import React from 'react';
import { createAppContainer, createSwitchNavigator } from 'react-navigation';

import MainTabNavigator from './MainTabNavigator';
import PlayScreen from '../screens/PlayScreen';

export default createAppContainer(
    createSwitchNavigator({
        Main: MainTabNavigator,
        Play: PlayScreen,
    })
);
